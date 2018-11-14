<?php
declare(strict_types=1);

namespace AlexTartan\GuzzlePsr18Adapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Client extends GuzzleClient implements ClientInterface
{

    /**
     * @throws ClientException
     * @throws NetworkException
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->send($request);
        } catch (GuzzleClientException $gCliEx) {
            throw ClientException::fromRequest($request, $gCliEx->getMessage(), $gCliEx->getCode(), $gCliEx);
        } catch (GuzzleConnectException $gConEx) {
            throw NetworkException::fromRequest($request, $gConEx->getMessage(), $gConEx->getCode(), $gConEx);
        } catch (GuzzleRequestException $gReqEx) {
            throw RequestException::fromRequest($request, $gReqEx->getMessage(), $gReqEx->getCode(), $gReqEx);
        }
    }
}
