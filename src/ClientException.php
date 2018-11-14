<?php
declare(strict_types=1);

namespace AlexTartan\GuzzlePsr18Adapter;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

final class ClientException extends Exception implements ClientExceptionInterface
{
    /** @ var RequestInterface */
    private $request;

    /**
     * PHP>7.2 allows overriding a public constructor with a private one,
     * but prior to this version, it throws a fatal error.
     *
     * Marking as internal to force passing the RequestInterface parameter as required by PSR-18
     *
     * @internal
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromRequest(
        RequestInterface $request,
        $message = '',
        $code = 0,
        Throwable $previous = null
    ): self {
        $exception          = new self($message, $code, $previous);
        $exception->request = $request;

        return $exception;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
