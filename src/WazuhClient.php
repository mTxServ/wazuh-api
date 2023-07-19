<?php

namespace Wazuh;

use Wazuh\Middleware\AuthMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\HandlerStack;

class WazuhClient extends Client
{
    public function __construct(array $config = [])
    {
        if (empty($config['wazuh_user'])) {
            throw new InvalidArgumentException('You must provide an wazuh_user key');
        }

        if (empty($config['wazuh_password'])) {
            throw new InvalidArgumentException('You must provide an wazuh_password key');
        }

        $config = array_merge([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ], $config);

        if (empty($config['handler'])) {
            $config['handler'] = HandlerStack::create();
        }
        
        $config['handler']->push(new AuthMiddleware(new Client([
            'base_uri' => $config['base_uri'],
            'wazuh_user' => $config['wazuh_user'],
            'wazuh_password' => $config['wazuh_password'],
            'verify' => isset($config['verify']) ? $config['verify'] : true,
        ])));

        parent::__construct($config);
    }
}
