<?php

declare(strict_types=1);

namespace AlexTartan\GuzzlePsr18Adapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class Client extends GuzzleClient
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
        } catch (GuzzleClientException | GuzzleServerException $gEx) {
            // This is thrown for 4xx of 5xx response status codes.
            return $this->extractGuzzleExceptionResponse($gEx, $request);
        } catch (GuzzleConnectException $gConEx) {
            // Network connectivity errors
            throw NetworkException::fromRequest($request, $gConEx->getMessage(), $gConEx->getCode(), $gConEx);
        } catch (GuzzleRequestException $gReqEx) {
            // Malformed request
            throw RequestException::fromRequest($request, $gReqEx->getMessage(), $gReqEx->getCode(), $gReqEx);
        } catch (Throwable $t) {
            // Request could not be sent
            throw ClientException::fromRequest($request, $t->getMessage(), $t->getCode(), $t);
        }
    }

    private function extractGuzzleExceptionResponse(BadResponseException $exception, RequestInterface $request): ResponseInterface
    {
        if ($exception->hasResponse()) {
            return $exception->getResponse();
        }

        // response is not a ResponseInterface instance
        throw ClientException::fromRequest($request, $exception->getMessage(), $exception->getCode(), $exception);
    }
}
