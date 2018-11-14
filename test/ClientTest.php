<?php
declare(strict_types=1);

namespace AlexTartanTest\GuzzlePsr18Adapter;

use AlexTartan\GuzzlePsr18Adapter\Client;
use AlexTartan\GuzzlePsr18Adapter\ClientException;
use AlexTartan\GuzzlePsr18Adapter\NetworkException;
use AlexTartan\GuzzlePsr18Adapter\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    /** @var Client */
    private $client;

    public function setUp()
    {
        parent::setUp();

        $this->client = new Client(['handler' => new MockHandler([new Response()])]);
    }

    public function testSendRequest()
    {
        $request = new Request('GET', 'http://some-domain.com');
        $r       = $this->client->sendRequest($request);
        TestCase::assertEquals(200, $r->getStatusCode());
    }

    public function testThrowsRequestException()
    {
        $this->expectException(RequestException::class);

        $request = new Request('GET', 'https://some-domain.com/404');
        $client  = new Client([
            'handler' => new MockHandler(
                [
                    new \GuzzleHttp\Exception\RequestException(
                        'Error Communicating with Server',
                        new Request('GET', 'test')
                    ),
                ]
            ),
        ]);
        $client->sendRequest($request);
    }

    public function testThrowsClientException()
    {
        $exceptionCaught = false;
        $request         = new Request('GET', 'http://foo.com');

        try {
            $mock    = new MockHandler([new Response(404)]);
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            $client->sendRequest($request);
        } catch (ClientException $ce) {
            $exceptionCaught = true;

            // and also check that the request object can be retireved
            self::assertSame($request, $ce->getRequest());
        }

        self::assertTrue($exceptionCaught);
    }

    public function testThrowsNetworkException()
    {
        $exceptionCaught = false;
        $request         = new Request('GET', 'https://some-domain.com/404');
        $client          = new Client([
            'handler' => new MockHandler(
                [
                    new \GuzzleHttp\Exception\ConnectException(
                        'Error Communicating with Server',
                        new Request('GET', 'test')
                    ),
                ]
            ),
        ]);

        try {
            $client->sendRequest($request);
        } catch (NetworkException $ce) {
            $exceptionCaught = true;

            // and also check that the request object can be retireved
            self::assertSame($request, $ce->getRequest());
        }

        self::assertTrue($exceptionCaught);
    }
}
