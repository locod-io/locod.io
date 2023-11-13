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

namespace App\Linear\Application\Query;

class LinearConfig
{
    public function __construct(
        protected string $endPoint,
        protected string $key,
        protected string $useLinearGlobally,
    ) {
    }

    public function getEndPoint(): string
    {
        return $this->endPoint;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getUseLinearGlobally(): string
    {
        return $this->useLinearGlobally;
    }

    public function setKey(string $key): void
    {
        if ($this->useLinearGlobally === 'false') {
            $this->key = $key;
        }
    }

    public static function checkConfig(LinearConfig $config): void
    {
        if (strlen($config->getKey()) === 0) {
            throw new \Exception('API Key is empty');
            exit;
        }
    }

}
