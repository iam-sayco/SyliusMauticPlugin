<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Http\Api;

use Mautic\Api\Api;

interface MauticApiInterface
{
    public function getApi(): Api;
}