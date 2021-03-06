#### Guzzle Adapter for PSR-18

Wrapper over Guzzle to comply with PSR-18

Current build status
===

![CI](https://github.com/alextartan/guzzle-psr18-adapter/workflows/CI/badge.svg?branch=master)
[![codecov](https://codecov.io/gh/alextartan/guzzle-psr18-adapter/branch/master/graph/badge.svg)](https://codecov.io/gh/alextartan/guzzle-psr18-adapter)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/alextartan/guzzle-psr18-adapter/master)](https://infection.github.io)
[![Downloads](https://img.shields.io/badge/dynamic/json.svg?url=https://repo.packagist.org/packages/alextartan/guzzle-psr18-adapter.json&label=Downloads&query=$.package.downloads.total&colorB=orange)](https://packagist.org/packages/alextartan/guzzle-psr18-adapter)

Install
===

The easiest way is to use `composer`:

    composer require alextartan/guzzle-psr18-adapter

Note: requires `PHP` >= 7.2

Usage
===

Example:

```
<?php

use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use GuzzleHttp\Psr7\Request;

[...]

public function sendSomething(ClientInterface $client, $data)
{
    // Using GuzzleHttp\Psr7\Request in this example, but any implementation of PSR-7 will do
    $request = new Request('GET', 'https://some-domain.com/something');
    try{
        // response is also an implementation of PSR-7 (Psr\Http\Message\ResponseInterface)
        $response = $client->sendRequest($request)
    } catch(ClientException $e){
        // do something
    } catch(NetworkException $e){
        // do something
    } catch(RequestException $e){
        // do something
    }
}

[...]

```

Issues and pull requests.
===

Any issues found should be reported in this repository issue tracker, issues will be fixed when possible.
Pull requests will be accepted, but please adhere to the PSR2 coding standard. All builds must pass in order to merge the PR.
