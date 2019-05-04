<?php
declare(strict_types=1);

namespace AlexTartanTest\GuzzlePsr18Adapter;

use AlexTartan\GuzzlePsr18Adapter\BasePsr18Exception;
use AlexTartan\GuzzlePsr18Adapter\ClientException;
use AlexTartan\GuzzlePsr18Adapter\NetworkException;
use AlexTartan\GuzzlePsr18Adapter\RequestException;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

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
    public function testConstruct(string $className, string $parentClassName, string $message, int $code, ?Throwable $previousException): void
    {
        /** @var BasePsr18Exception $className */
        $exception = $className::fromRequest($this->request, $message, $code, $previousException);

        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf($className, $exception);
        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf($parentClassName, $exception);

        self::assertSame($message, $exception->getMessage());
        self::assertSame($code, $exception->getCode());
        self::assertSame($previousException, $exception->getPrevious());
    }

    /**
     * @dataProvider getRequestDataProvider
     */
    public function testGetRequest(string $className): void
    {
        /** @var BasePsr18Exception $className */
        $exception = $className::fromRequest($this->request, '', 0, null);

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
        $previousException = new Exception('this happened before the psr18 exception');

        return [
            // no prev exception
            [NetworkException::class, NetworkExceptionInterface::class, 'msg1', 123, null],
            [RequestException::class, RequestExceptionInterface::class, 'msg2', 456, null],
            [ClientException::class, ClientExceptionInterface::class, 'msg3', 789, null],
            // with prev exception
            [NetworkException::class, NetworkExceptionInterface::class, 'msg1', 123, $previousException],
            [RequestException::class, RequestExceptionInterface::class, 'msg2', 456, $previousException],
            [ClientException::class, ClientExceptionInterface::class, 'msg3', 789, $previousException],
        ];
    }
}
