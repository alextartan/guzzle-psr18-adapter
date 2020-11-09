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

final class ClientTest extends TestCase
{
    private Client $client;

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

    /**
     * @param        class-string<\Throwable> $expectedExceptionClass
     *
     * @dataProvider exceptionDataProvider
     */
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

    /**
     * @return array<int, array<mixed>>
     */
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
                GuzzleClientException::create(
                    new Request('GET', 'test'),
                    null,
                ),
            ],
            [
                ClientExceptionInterface::class,
                new Exception('Some random exception'),
            ],
            [
                ClientExceptionInterface::class,
                (function () {
                    $exception = $this->createMock(GuzzleClientException::class);
                    $exception->expects(self::once())->method('hasResponse')->willReturn(false);

                    return $exception;
                })(),
            ],
        ];
    }
}
