#### Guzzle Adapter for PSR-18

Wrapper over Guzzle to comply with PSR-18

Current build status
===

[![Build Status](https://travis-ci.org/alextartan/guzzle-psr18-adapter.svg?branch=master)](https://travis-ci.org/alextartan/guzzle-psr18-adapter)
[![Coverage Status](https://coveralls.io/repos/github/alextartan/guzzle-psr18-adapter/badge.svg?branch=master)](https://coveralls.io/github/alextartan/guzzle-psr18-adapter?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alextartan/guzzle-psr18-adapter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alextartan/guzzle-psr18-adapter/?branch=master)
[![Packagist](https://img.shields.io/badge/Packagist-alextartan%2Fguzzle--psr18--adapter-blue.svg)](https://packagist.org/packages/alextartan/guzzle-psr18-adapter)
[![Downloads](https://img.shields.io/badge/dynamic/json.svg?url=https://repo.packagist.org/packages/alextartan/guzzle-psr18-adapter.json&label=Downloads&query=$.package.downloads.total&colorB=orange)](https://packagist.org/packages/alextartan/guzzle-psr18-adapter)

Install
===

The easiest way is to use `composer`:

    composer require alextartan/guzzle-psr18-adapter

Note: requires `PHP` >= 7.0

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