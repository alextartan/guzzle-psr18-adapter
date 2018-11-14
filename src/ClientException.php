<?php
declare(strict_types=1);

namespace AlexTartan\GuzzlePsr18Adapter;

use Psr\Http\Client\ClientExceptionInterface;

final class ClientException extends BasePsr18Exception implements ClientExceptionInterface
{
}
