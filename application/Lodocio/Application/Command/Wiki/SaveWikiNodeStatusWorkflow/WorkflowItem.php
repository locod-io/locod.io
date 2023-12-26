<?php

/*
 * This file is part of the Lodoc.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Lodocio\Application\Command\Wiki\SaveWikiNodeStatusWorkflow;

class WorkflowItem
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    private function __construct(
        protected string    $id,
        protected string    $label,
        protected \stdClass $position,
        protected array     $flowIn,
        protected array     $flowOut,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Hydration
    // ———————————————————————————————————————————————————————————————

    public static function hydrateFromJson($json): self
    {
        return new self(
            $json->id,
            $json->label,
            $json->position,
            self::convertArray($json->flowIn),
            self::convertArray($json->flowOut)
        );
    }

    private static function convertArray(array $in): array
    {
        $result = [];
        foreach ($in as $item) {
            $result[] = intval($item);
        }
        return $result;
    }

    // ———————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getPosition(): \stdClass
    {
        return $this->position;
    }

    public function getFlowIn(): array
    {
        return $this->flowIn;
    }

    public function getFlowOut(): array
    {
        return $this->flowOut;
    }
}
