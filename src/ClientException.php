<?php
declare(strict_types=1);

namespace AlexTartan\GuzzlePsr18Adapter;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;

final class ClientException extends Exception implements ClientExceptionInterface
{
}
