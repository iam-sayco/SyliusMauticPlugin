<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Http;

use Mautic\Api\Api;

interface MauticClientInterface
{
    public function getApi(string $context): Api;
}
