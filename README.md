# Wazuh API PHP Client

![Latest Stable Version](https://poser.pugx.org/mtxserv/wazuh-api/v/stable.png)

The Wazuh API PHP Client is a modern library built on top of [Guzzle](http://docs.guzzlephp.org/en/stable/), providing an efficient interface for interacting with the [Wazuh REST API](https://documentation.wazuh.com/current/user-manual/api/index.html).

## Requirements

- PHP 7 or 8

## Installation

The recommended way to install the Wazuh API PHP Client is via [Composer](https://getcomposer.org/), a powerful package manager for PHP:

```sh
composer require mtxserv/wazuh-api
```

## Usage

Below is a basic example illustrating how to instantiate the client and retrieve a list of agents:

```php
<?php

use Wazuh\WazuhClient;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

require_once 'vendor/autoload.php';

// Setup WazuhClient with necessary parameters
$client = new WazuhClient([
    'base_uri' => 'https://wazuh.my.instance:55000',
    'wazuh_user' => 'my_user',
    'wazuh_password' => 'my_password',
    'verify' => true, // SSL Certificate verification
]);

try {
    // Retrieve list of agents
    $response = $client->get('/agents');

    // Decode JSON response and handle JSON exceptions
    try {
        $json = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $jsonException) {
        echo 'JSON decoding error: ', $jsonException->getMessage(), "\n";
        return;
    }

    var_dump($json);
} catch (GuzzleException $e) {
    echo 'HTTP request error: ', $e->getMessage(), "\n";
}

```

In this example, we're connecting to a Wazuh instance, authenticating with a username and password, and requesting a list of agents. We're also handling any potential exceptions that might be thrown during this process.

## Support

For more examples and usage instructions, please refer to the [official Wazuh API documentation](https://documentation.wazuh.com/current/user-manual/api/index.html).

If you encounter any issues, feel free to open an issue on this GitHub repository.
