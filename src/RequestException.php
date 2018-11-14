<?php
declare(strict_types=1);

namespace AlexTartan\GuzzlePsr18Adapter;

use Psr\Http\Client\RequestExceptionInterface;

final class RequestException extends BasePsr18Exception implements RequestExceptionInterface
{
}
