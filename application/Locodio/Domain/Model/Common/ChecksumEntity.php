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

namespace App\Locodio\Domain\Model\Common;

use Doctrine\ORM\Mapping as ORM;

trait ChecksumEntity
{
    #[ORM\Column(length: 36)]
    private string $checksum = '';

    // ———————————————————————————————————————————————————————————————————
    // Getters and setters
    // ———————————————————————————————————————————————————————————————————

    public function getChecksum(): string
    {
        return $this->checksum;
    }

    public function setChecksum(): void
    {
        $reflect = new \ReflectionClass($this);
        $contentString = hash('sha256', $_ENV['APP_SECRET']);
        foreach ($reflect->getProperties() as $property) {
            if (!(
                'checksum' === $property->getName()
                || 'id' === $property->getName()
                || 'createdAt' === $property->getName()
                || 'updatedAt' === $property->getName()
                || 'createdBy' === $property->getName()
                || 'updatedBy' === $property->getName()
                || 'projects' === $property->getName()
                || 'domainModels' === $property->getName()
                || 'enums' === $property->getName()
                || 'templates' === $property->getName()
                || 'queries' === $property->getName()
                || 'commands' === $property->getName()
                || 'attributes' === $property->getName()
                || 'associations' === $property->getName()
                || 'options' === $property->getName()
                || 'enum' === $property->getName()
                || 'modelSettings' === $property->getName()
                || 'modelStatus' === $property->getName()
                || 'modules' === $property->getName()
                || 'masterTemplates' === $property->getName()
                || 'masterTemplate' === $property->getName()
                || 'masterTemplateLinkedAt' === $property->getName()
                || 'docProject' === $property->getName()
                || 'trackers' === $property->getName()
                || 'userInvitationLinks' === $property->getName()
                || 'organisationUsers' === $property->getName()
                || 'lazyPropertiesNames' === $property->getName()
                || 'lazyPropertiesDefaults' === $property->getName()
            )) {
                match (gettype($this->{$property->getName()})) {
                    'string' => $contentString .= $this->{$property->getName()},
                    'boolean', 'integer' => $contentString .= (string) $this->{$property->getName()},
                    default => $contentString .= json_encode($this->{$property->getName()}, \JSON_THROW_ON_ERROR),
                };
            }
        }
        $this->checksum = md5($contentString);
    }
}
