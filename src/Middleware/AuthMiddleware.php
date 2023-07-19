<?php

namespace Wazuh\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class AuthMiddleware
{
    private $token;

    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    
    public function __invoke(callable $handler)
    {
        if (empty($this->token)) {
            $this->generateToken();
        }

        return function (Request $request, array $options) use ($handler) {
            return $handler(
                $request->withHeader('Authorization', sprintf('Bearer %s',  $this->token)),
                $options
            );
        };
    }

    private function generateToken()
    {
        $response = $this->client->post('/security/user/authenticate', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'auth' => [
                $this->client->getConfig('wazuh_user'), 
                $this->client->getConfig('wazuh_password')
            ],
        ]);

        $json = json_decode($response->getBody()->getContents(), \JSON_THROW_ON_ERROR);
        if (!isset($json['error'])) {
            throw new GuzzleException('Error returned by the api is missing');
        }

        if ($json['error'] !== 0) {
            throw new GuzzleException(sprintf('Error "%s" returned by the api is missing', $json['error']));
        }

        if (!isset($json['data'])) {
            throw new GuzzleException('Data returned by the api is missing');
        }

        if (!isset($json['data']['token'])) {
            throw new GuzzleException('Token returned by the api is missing');
        }

        $this->token = $json['data']['token'];
    }
}