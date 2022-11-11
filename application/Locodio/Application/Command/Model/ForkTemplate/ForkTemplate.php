<?php

namespace App\Locodio\Application\Command\Model\ForkTemplate;

class ForkTemplate
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected string $userId,
        protected string $templateId,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson(\stdClass $json): self
    {
        return new self(
            $json->userId,
            $json->templateId
        );
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getTemplateId(): string
    {
        return $this->templateId;
    }
}
