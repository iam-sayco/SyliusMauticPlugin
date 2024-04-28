<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin\Http;

use Mautic\Api\Api;
use Mautic\Auth\ApiAuth;
use Mautic\Auth\AuthInterface;
use Mautic\MauticApi;
use Sayco\SyliusMauticPlugin\Http\Exception\UnsupportedAuthenticationTypeException;

final class MauticClient implements MauticClientInterface
{
    public function __construct(
        private array $authConfig,
    ) {
    }

    public function getApi(string $context): Api
    {
        $api = new MauticApi();

        return $api->newApi($context, $this->getAuth(), $this->authConfig['baseUrl']);
    }

    private function getAuth(): AuthInterface
    {
        $authType = $this->getAuthenticationType();
        $initAuth = new ApiAuth();

        if ($this->isBasicAuth()) {
            return $initAuth->newAuth($this->authConfig, $authType);
        }

        throw new UnsupportedAuthenticationTypeException(
            "The authentication type '{$authType}' is not supported. Currently supported types are: 'BasicAuth'"
        );
    }

    private function getAuthenticationType(): string
    {
        $version = $this->authConfig['version'] ?? '';

        return $version ?: 'BasicAuth';
    }

    private function isBasicAuth(): bool
    {
        return 'BasicAuth' === $this->getAuthenticationType();
    }
}
