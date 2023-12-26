<?php

/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\SsoConnect;

class ProviderConfiguration
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    private function __construct(
        protected string $provider,
        protected string $driver,
        protected string $clientId,
        protected string $clientSecret,
        protected string $issuerUrl,
        protected string $identifierKey
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Maker
    // ——————————————————————————————————————————————————————————————————————————

    public static function make(
        string $provider,
        string $driver,
        string $clientId,
        string $clientSecret,
        string $issuerUrl,
        string $identifierKey,
    ): self {
        return new self(
            provider: $provider,
            driver: $driver,
            clientId: $clientId,
            clientSecret: $clientSecret,
            issuerUrl: $issuerUrl,
            identifierKey: $identifierKey,
        );
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Getters
    // ——————————————————————————————————————————————————————————————————————————

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getIssuerUrl(): string
    {
        return $this->issuerUrl;
    }

    public function getIdentifierKey(): string
    {
        return $this->identifierKey;
    }

}
