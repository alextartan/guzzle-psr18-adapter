<?php
declare(strict_types=1);

namespace AlexTartanTest\GuzzlePsr18Adapter;

use AlexTartan\GuzzlePsr18Adapter\ClientException;
use AlexTartan\GuzzlePsr18Adapter\NetworkException;
use AlexTartan\GuzzlePsr18Adapter\RequestException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @covers \AlexTartan\GuzzlePsr18Adapter\BasePsr18Exception
 */
final class BasePsr18ExceptionTest extends TestCase
{
    public function testConstructClientException()
    {
        $request   = $this->createMock(RequestInterface::class);
        $exception = ClientException::fromRequest($request, 'msg', 123);

        self::assertInstanceOf(ClientException::class, $exception);
        self::assertInstanceOf(ClientExceptionInterface::class, $exception);
        self::assertSame('msg', $exception->getMessage());
        self::assertSame(123, $exception->getCode());
    }

    public function testConstructRequestException()
    {
        $request   = $this->createMock(RequestInterface::class);
        $exception = RequestException::fromRequest($request, 'msg', 123);

        self::assertInstanceOf(RequestException::class, $exception);
        self::assertInstanceOf(RequestExceptionInterface::class, $exception);
        self::assertSame('msg', $exception->getMessage());
        self::assertSame(123, $exception->getCode());
    }

    public function testConstructNetworkException()
    {
        $request   = $this->createMock(RequestInterface::class);
        $exception = NetworkException::fromRequest($request, 'msg', 123);

        self::assertInstanceOf(NetworkException::class, $exception);
        self::assertInstanceOf(NetworkExceptionInterface::class, $exception);
        self::assertSame('msg', $exception->getMessage());
        self::assertSame(123, $exception->getCode());
    }

    public function testGetRequestFromNetworkException()
    {
        $request   = $this->createMock(RequestInterface::class);
        $exception = NetworkException::fromRequest($request, 'msg', 123);

        self::assertSame($request, $exception->getRequest());
    }

    public function testGetRequestFromRequestException()
    {
        $request   = $this->createMock(RequestInterface::class);
        $exception = RequestException::fromRequest($request, 'msg', 123);

        self::assertSame($request, $exception->getRequest());
    }

    public function testGetRequestFromClientException()
    {
        $request   = $this->createMock(RequestInterface::class);
        $exception = ClientException::fromRequest($request, 'msg', 123);

        self::assertSame($request, $exception->getRequest());
    }
}
