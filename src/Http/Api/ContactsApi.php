<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Http\Api;

use Mautic\Api\Api;
use Mautic\Api\Contacts;
use Sayco\SyliusMauticPlugin\Http\MauticClientInterface;

class ContactsApi implements ContactsApiInterface
{
    public ?Contacts $api = null;

    public function __construct(
        private MauticClientInterface $mauticClient,
    ) {
    }

    public function getContactByEmail(string $email): ?array
    {
        $contacts = $this->getApi()->getList($email, limit: 1, minimal: true);
        if (empty($contacts['contacts'])) {
            return null;
        }

        $contact = reset($contacts['contacts']);

        return $contact;
    }

    public function getApi(): Api
    {
        if (null === $this->api) {
            $this->api = $this->mauticClient->getApi('contacts');
        }

        return $this->api;
    }
}
