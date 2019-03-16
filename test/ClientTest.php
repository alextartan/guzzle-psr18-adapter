<?php
declare(strict_types=1);

namespace AlexTartanTest\GuzzlePsr18Adapter;

use AlexTartan\GuzzlePsr18Adapter\Client;
use AlexTartan\GuzzlePsr18Adapter\NetworkException;
use AlexTartan\GuzzlePsr18Adapter\RequestException;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * @covers \AlexTartan\GuzzlePsr18Adapter\Client
 * @covers \AlexTartan\GuzzlePsr18Adapter\ClientException
 * @covers \AlexTartan\GuzzlePsr18Adapter\NetworkException
 * @covers \AlexTartan\GuzzlePsr18Adapter\RequestException
 */
final class ClientTest extends TestCase
{

    /** @var Client */
    private $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = new Client(['handler' => new MockHandler([new Response()])]);
    }

    public function testSendRequest(): void
    {
        $request = new Request('GET', 'http://some-domain.com');
        $r       = $this->client->sendRequest($request);
        self::assertEquals(200, $r->getStatusCode());
    }

    public function testThrowsRequestException(): void
    {
        $exceptionCaught = false;
        $request         = new Request('GET', 'https://some-domain.com/404');
        $client          = new Client(
            [
                'handler' => new MockHandler(
                    [
                        new GuzzleRequestException(
                            'Error Communicating with Server',
                            new Request('GET', 'test')
                        ),
                    ]
                ),
            ]
        );

        try {
            $client->sendRequest($request);
        } catch (RequestException $re) {
            $exceptionCaught = true;

            // and also check that the request object can be retrieved
            self::assertSame($request, $re->getRequest());
        }

        self::assertTrue($exceptionCaught);
    }

    public function testClientErrorCodeDoesNotThrowException(): void
    {
        $request  = new Request('GET', 'http://foo.com');
        $mock     = new MockHandler([new Response(404)]);
        $handler  = HandlerStack::create($mock);
        $client   = new Client(['handler' => $handler]);
        $response = $client->sendRequest($request);

        self::assertSame(404, $response->getStatusCode());
    }

    public function testCatchGuzzleClientException(): void
    {
        $exceptionCaught = false;
        $request         = new Request('GET', 'https://some-domain.com/404');
        $client          = new Client(
            [
                'handler' => new MockHandler(
                    [
                        new GuzzleClientException(
                            'got 4xx response',
                            new Request('GET', 'test')
                        ),
                    ]
                ),
            ]
        );

        try {
            $client->sendRequest($request);
        } catch (ClientExceptionInterface $ce) {
            $exceptionCaught = true;
        }

        self::assertTrue($exceptionCaught);
    }

    public function testServerErrorCodeDoesNotThrowException(): void
    {
        $request  = new Request('GET', 'http://foo.com');
        $mock     = new MockHandler([new Response(501)]);
        $handler  = HandlerStack::create($mock);
        $client   = new Client(['handler' => $handler]);
        $response = $client->sendRequest($request);

        self::assertSame(501, $response->getStatusCode());
    }

    public function testThrowsNetworkException(): void
    {
        $exceptionCaught = false;
        $request         = new Request('GET', 'https://some-domain.com/404');
        $client          = new Client(
            [
                'handler' => new MockHandler(
                    [
                        new GuzzleConnectException(
                            'Error Communicating with Server',
                            new Request('GET', 'test')
                        ),
                    ]
                ),
            ]
        );

        try {
            $client->sendRequest($request);
        } catch (NetworkException $ce) {
            $exceptionCaught = true;

            // and also check that the request object can be retrieved
            self::assertSame($request, $ce->getRequest());
        }

        self::assertTrue($exceptionCaught);
    }
}
