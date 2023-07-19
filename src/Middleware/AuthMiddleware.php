<?php

namespace Wazuh\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

/**
 * Class AuthMiddleware
 * Middleware for handling authentication.
 */
class AuthMiddleware
{
    private $token;
    private $client;

    /**
     * AuthMiddleware constructor.
     *
     * @param Client $client The GuzzleHttp client.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    
    /**
     * Generates a token if it does not exist and returns a function that 
     * includes the Authorization header with each request.
     *
     * @param callable $handler The handler function.
     *
     * @return callable The function to handle the request.
     */

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

    /**
     * Generates a token using the /security/user/authenticate API endpoint.
     *
     * @throws GuzzleException if any error occurs while generating the token.
     */
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

        $json = json_decode($response->getBody()->getContents(), true);

        if (empty($json) || !isset($json['error'], $json['data']['token'])) {
            throw new GuzzleException('Unexpected response received from the API.');
        }

        if ($json['error'] !== 0) {
            throw new GuzzleException(sprintf('API error: "%s"', $json['error']));
        }

        $this->token = $json['data']['token'];
    }
}