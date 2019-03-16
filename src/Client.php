<?php
declare(strict_types=1);

namespace AlexTartan\GuzzlePsr18Adapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TypeError;

final class Client extends GuzzleClient implements ClientInterface
{

    /**
     * As required by PSR-18, this method needs to throw specific exceptions.
     * Catching Guzzle exceptions and re-throwing them as PSR-18 compliant exceptions.
     *
     * @see https://www.php-fig.org/psr/psr-18/#error-handling
     * @see http://docs.guzzlephp.org/en/stable/quickstart.html#exceptions
     *
     * @throws ClientException
     * @throws NetworkException
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->send($request);
        } catch (GuzzleClientException $gCliEx) {
            // This is thrown for 4xx response status codes.
            try {
                return $gCliEx->getResponse();
            } catch (TypeError $te) {
                // response is not a ResponseInterface instance
                throw ClientException::fromRequest($request, $te->getMessage(), $te->getCode());
            }
        } catch (GuzzleServerException $gSrvEx) {
            // This is thrown for 5xx response status codes.
            try {
                return $gSrvEx->getResponse();
            } catch (TypeError $te) {
                // response is not a ResponseInterface instance
                throw ClientException::fromRequest($request, $te->getMessage(), $te->getCode());
            }
        } catch (GuzzleConnectException $gConEx) {
            // Network connectivity errors
            throw NetworkException::fromRequest($request, $gConEx->getMessage(), $gConEx->getCode());
        } catch (GuzzleRequestException $gReqEx) {
            // Malformed request
            throw RequestException::fromRequest($request, $gReqEx->getMessage(), $gReqEx->getCode());
        } catch (\Throwable $t) {
            // Request could not be sent
            throw ClientException::fromRequest($request, $t->getMessage(), $t->getCode());
        }
    }
}
