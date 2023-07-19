<?php

namespace Wazuh;

use Wazuh\Middleware\AuthMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\HandlerStack;

/**
 * Class WazuhClient
 * This class extends from GuzzleHttp's Client and uses Wazuh's AuthMiddleware for authentication.
 */
class WazuhClient extends Client
{
    /**
     * WazuhClient constructor.
     *
     * @param array $config Configuration array which includes 'wazuh_user' and 'wazuh_password' keys.
     *
     * @throws InvalidArgumentException If 'wazuh_user' or 'wazuh_password' key is not provided in the config.
     */
    public function __construct(array $config = [])
    {
        // Checking if 'wazuh_user' key is provided in the config
        if (!isset($config['wazuh_user']) || empty($config['wazuh_user'])) {
            throw new InvalidArgumentException('You must provide a wazuh_user key');
        }

        // Checking if 'wazuh_password' key is provided in the config
        if (!isset($config['wazuh_password']) || empty($config['wazuh_password'])) {
            throw new InvalidArgumentException('You must provide a wazuh_password key');
        }

        // Defining default headers
        $config = array_merge([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ], $config);

        // If handler is not provided, create a new one
        if (!isset($config['handler']) || empty($config['handler'])) {
            $config['handler'] = HandlerStack::create();
        }
        
        // Pushing AuthMiddleware into the handler
        $config['handler']->push(new AuthMiddleware(new Client([
            'base_uri' => isset($config['base_uri']) ? $config['base_uri'] : '',
            'wazuh_user' => $config['wazuh_user'],
            'wazuh_password' => $config['wazuh_password'],
            'verify' => isset($config['verify']) ? $config['verify'] : true,
        ])));

        // Calling parent constructor with the modified config
        parent::__construct($config);
    }
}
