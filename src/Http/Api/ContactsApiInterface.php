<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Http\Api;

interface ContactsApiInterface extends MauticApiInterface
{
    public function getContactByEmail(string $email): ?array;
}
