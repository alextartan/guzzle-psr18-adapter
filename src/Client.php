<?php
declare(strict_types=1);

namespace AlexTartan\GuzzlePsr18Adapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Client extends GuzzleClient implements ClientInterface
{
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->send($request);
        } catch (\Throwable $t) {
            throw new ClientException($t->getMessage(), $request, null, $t);
        }
    }
}
