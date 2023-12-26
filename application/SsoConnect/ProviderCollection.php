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

class ProviderCollection
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected array $providers = []
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Add configuration
    // ——————————————————————————————————————————————————————————————————————————

    public function add(ProviderConfiguration $provider): void
    {
        $this->providers[] = $provider;
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Getters
    // ——————————————————————————————————————————————————————————————————————————

    /**
     * @return ProviderConfiguration[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    public function getProviderByName(string $provider): ?ProviderConfiguration
    {
        foreach ($this->providers as $providerConfiguration) {
            if (strtolower($providerConfiguration->getProvider()) === strtolower($provider)) {
                return $providerConfiguration;
            }
        }
        return null;
    }

}
