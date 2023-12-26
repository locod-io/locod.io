<?php

declare(strict_types=1);

namespace App\Figma;

class FigmaConfig
{
    public function __construct(
        protected string $endPoint,
        protected string $key,
        protected string $useFigmaGlobally,
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

    public function getUseFigmaGlobally(): string
    {
        return $this->useFigmaGlobally;
    }

    public function setKey(string $key): void
    {
        if ($this->useFigmaGlobally === 'false') {
            $this->key = $key;
        }
    }

    public static function checkConfig(FigmaConfig $config): void
    {
        if (strlen($config->getKey()) === 0) {
            throw new \Exception('API Key is empty');
            exit;
        }
    }

}
