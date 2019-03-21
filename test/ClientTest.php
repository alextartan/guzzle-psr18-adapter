<?php
declare(strict_types=1);

namespace AlexTartanTest\GuzzlePsr18Adapter;

use AlexTartan\GuzzlePsr18Adapter\Client;
use Exception;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;

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

    public function testClientErrorCodeDoesNotThrowException(): void
    {
        $request  = new Request('GET', 'http://foo.com');
        $mock     = new MockHandler([new Response(404)]);
        $handler  = HandlerStack::create($mock);
        $client   = new Client(['handler' => $handler]);
        $response = $client->sendRequest($request);

        self::assertSame(404, $response->getStatusCode());
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

    /** @dataProvider exceptionDataProvider */
    public function testThrowsRequestException(string $expectedExceptionClass, Exception $expectedException): void
    {
        $this->expectException($expectedExceptionClass);

        $request = new Request('GET', 'https://some-domain.com/404');
        $client  = new Client(
            [
                'handler' => new MockHandler(
                    [
                        $expectedException,
                    ]
                ),
            ]
        );

        $client->sendRequest($request);
    }

    public function exceptionDataProvider(): array
    {
        return [
            [
                RequestExceptionInterface::class,
                new GuzzleRequestException(
                    'Error Communicating with Server',
                    new Request('GET', 'test')
                ),
            ],
            [
                NetworkExceptionInterface::class,
                new GuzzleConnectException(
                    'Error Communicating with Server',
                    new Request('GET', 'test')
                ),
            ],
            [
                ClientExceptionInterface::class,
                new GuzzleClientException(
                    'got 4xx response',
                    new Request('GET', 'test'),
                    null // new Response(501) -> still need to check that ClientExceptionInterface is thrown when Response is null
                ),
            ],
            [
                ClientExceptionInterface::class,
                new Exception('Some random exception'),
            ],
        ];
    }
}
