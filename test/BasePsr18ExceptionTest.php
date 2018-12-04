<?php
declare(strict_types=1);

namespace AlexTartanTest\GuzzlePsr18Adapter;

use AlexTartan\GuzzlePsr18Adapter\BasePsr18Exception;
use AlexTartan\GuzzlePsr18Adapter\ClientException;
use AlexTartan\GuzzlePsr18Adapter\NetworkException;
use AlexTartan\GuzzlePsr18Adapter\RequestException;
use PHPUnit\Framework\MockObject\MockObject;
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
    /** @var RequestInterface|MockObject */
    private $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(RequestInterface::class);
    }

    /**
     * @dataProvider constructorDataProvider
     */
    public function testConstruct(string $className, string $parentClassName, string $message, int $code): void
    {
        /** @var BasePsr18Exception $className */
        $exception = $className::fromRequest($this->request, $message, $code);

        self::assertInstanceOf($className, $exception);
        self::assertInstanceOf($parentClassName, $exception);
        self::assertSame($message, $exception->getMessage());
        self::assertSame($code, $exception->getCode());
    }

    /**
     * @dataProvider getRequestDataProvider
     */
    public function testGetRequest(string $className): void
    {
        /** @var BasePsr18Exception $className */
        $exception = $className::fromRequest($this->request);

        self::assertSame($this->request, $exception->getRequest());
    }

    public function getRequestDataProvider(): array
    {
        return [
            [NetworkException::class],
            [RequestException::class],
            [ClientException::class],
        ];
    }

    public function constructorDataProvider(): array
    {
        return [
            [NetworkException::class, NetworkExceptionInterface::class, 'msg1', 123],
            [RequestException::class, RequestExceptionInterface::class, 'msg2', 456],
            [ClientException::class, ClientExceptionInterface::class, 'msg3', 789],
        ];
    }
}
