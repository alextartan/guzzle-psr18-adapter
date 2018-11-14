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
            throw ClientException::fromRequest($request, $gCliEx->getMessage(), $gCliEx->getCode());
        } catch (GuzzleConnectException $gConEx) {
            throw NetworkException::fromRequest($request, $gConEx->getMessage(), $gConEx->getCode());
        } catch (GuzzleRequestException $gReqEx) {
            throw RequestException::fromRequest($request, $gReqEx->getMessage(), $gReqEx->getCode());
        }
    }
}
