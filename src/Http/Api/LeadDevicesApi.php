<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Http\Api;

use Mautic\Api\Api;
use Mautic\Api\Stats;
use Sayco\SyliusMauticPlugin\Http\MauticClientInterface;

class LeadDevicesApi implements LeadDevicesApiInterface
{
    private ?Stats $api = null;

    public function __construct(
        private MauticClientInterface $mauticClient,
    ) {
    }

    public function getLeadIdByTrackingId(string $tracking_id): ?int
    {
        /** @var Stats $api */
        $api = $this->getApi();

        $result = $api->get(
            table: 'lead_devices',
            limit: 1,
            order: [
                [
                    'col' => 'id',
                    'dir' => 'asc',
                ],
            ],
            where: [
                [
                    'col' => 'id',
                    'expr' => 'tracking_id',
                    'val' => $tracking_id,
                ],
            ]
        );

        if (empty($result['stats'])) {
            return null;
        }

        $stat = reset($result['stats']);

        return (int) $stat['lead_id'];
    }

    public function getApi(): Api
    {
        if (null === $this->api) {
            /** @var Stats $api */
            $api = $this->mauticClient->getApi('stats');
            $this->api = $api;
        }

        return $this->api;
    }
}
