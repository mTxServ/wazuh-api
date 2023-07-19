Wazuh API
==========

[![Latest Stable Version](https://poser.pugx.org/mtxserv/wazuh-api/v/stable.png)](https://packagist.org/packages/mtxserv/wazuh-api)

Wazuh Api is a modern PHP library based on Guzzle for [Wazuh Rest API](https://documentation.wazuh.com/current/user-manual/api/index.html).

## Dependencies

* PHP 7 / 8
* [Guzzle](http://www.guzzlephp.org): ^7.0

## Installation

Installation of Wazuh Rest Api is only officially supported using Composer:

```sh
composer require mtxserv/wazuh-api
```

## Example

```php
<?php

use Wazuh\WazuhClient;
use GuzzleHttp\Exception\GuzzleException;

$client = new WazuhClient([
    'base_uri' => 'https://wazuh.my.instance:55000',
    'wazuh_user' => 'my_user',
    'wazuh_password' => 'my_password',
    'verify' => true,
]);

try {
    // Get VM list
    $response = $client->get('/agents');
    $json = json_decode($response->getBody()->getContents(), \JSON_THROW_ON_ERROR);
    var_dump($json);
} catch (GuzzleException $e) {
    var_dump($e->getMessage());
}
```
