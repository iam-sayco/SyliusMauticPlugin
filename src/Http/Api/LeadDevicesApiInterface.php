<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Http\Api;

interface LeadDevicesApiInterface extends MauticApiInterface
{
    public function getLeadIdByTrackingId(string $tracking_id): ?int;
}
