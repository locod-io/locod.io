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

class ProviderFactory
{
    public static function makeProviderCollection(array $config): ProviderCollection
    {
        $providerCollection = new ProviderCollection();
        if (isset($config['AUTH_PROVIDERS'])) {
            $providerKeysValue = trim($config['AUTH_PROVIDERS']);
            $providerKeys = explode(',', $providerKeysValue);

            foreach ($providerKeys as $providerKey) {
                $providerKey = trim($providerKey);
                if (isset($config["AUTH_" . strtoupper($providerKey) . "_DRIVER"])) {
                    $driver = $config["AUTH_" . strtoupper($providerKey) . "_DRIVER"];
                    if ($driver === 'openid') {
                        if (isset($config["AUTH_" . strtoupper($providerKey) . "_CLIENT_ID"]) &&
                            isset($config["AUTH_" . strtoupper($providerKey) . "_CLIENT_SECRET"]) &&
                            isset($config["AUTH_" . strtoupper($providerKey) . "_ISSUER_URL"]) &&
                            isset($config["AUTH_" . strtoupper($providerKey) . "_IDENTIFIER_KEY"])) {
                            $providerCollection->add(
                                ProviderConfiguration::make(
                                    provider: strtoupper($providerKey),
                                    driver: $driver,
                                    clientId: $config["AUTH_" . strtoupper($providerKey) . "_CLIENT_ID"],
                                    clientSecret: $config["AUTH_" . strtoupper($providerKey) . "_CLIENT_SECRET"],
                                    issuerUrl: $config["AUTH_" . strtoupper($providerKey) . "_ISSUER_URL"],
                                    identifierKey: $config["AUTH_" . strtoupper($providerKey) . "_IDENTIFIER_KEY"],
                                )
                            );
                        }
                    } elseif ($driver === 'github') {
                        if (isset($config["AUTH_" . strtoupper($providerKey) . "_CLIENT_ID"]) &&
                            isset($config["AUTH_" . strtoupper($providerKey) . "_CLIENT_SECRET"])) {
                            $providerCollection->add(
                                ProviderConfiguration::make(
                                    provider: strtoupper($providerKey),
                                    driver: $driver,
                                    clientId: $config["AUTH_" . strtoupper($providerKey) . "_CLIENT_ID"],
                                    clientSecret: $config["AUTH_" . strtoupper($providerKey) . "_CLIENT_SECRET"],
                                    issuerUrl: '',
                                    identifierKey: $config["AUTH_" . strtoupper($providerKey) . "_IDENTIFIER_KEY"],
                                )
                            );
                        }
                    }
                }
            }
        }
        return $providerCollection;
    }
}
