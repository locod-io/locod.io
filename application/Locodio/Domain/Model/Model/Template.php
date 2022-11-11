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

namespace App\Locodio\Domain\Model\Model;

use App\Locodio\Domain\Model\Common\ChecksumEntity;
use App\Locodio\Domain\Model\Common\EntityId;
use App\Locodio\Domain\Model\Common\SequenceEntity;
use App\Locodio\Domain\Model\Organisation\Project;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\TemplateRepository::class)]
class Template
{
    // ———————————————————————————————————————————————————————————————————————————————————————
    // Properties
    // ———————————————————————————————————————————————————————————————————————————————————————

    use EntityId;
    use SequenceEntity;
    use TimestampableEntity;
    use BlameableEntity;
    use ChecksumEntity;

    #[ORM\Column(length: 25)]
    private string $type;

    #[ORM\Column(length: 191)]
    private string $name;

    #[ORM\Column(length: 25)]
    private string $language;

    #[ORM\Column(type: 'text')]
    private string $template;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Relations
    // ———————————————————————————————————————————————————————————————————————————————————————

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Organisation\Project", fetch: "EXTRA_LAZY", inversedBy: "templates")]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\Model\MasterTemplate", fetch: "EXTRA_LAZY", inversedBy: "templates")]
    #[ORM\JoinColumn(nullable: true)]
    private ?MasterTemplate $masterTemplate;

    #[ORM\Column(nullable: true, options: ["default" => '1970-01-01'])]
    private ?\DateTimeImmutable $masterTemplateLinkedAt;

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————————————————————————————

    private function __construct(Project $project, Uuid $uuid, TemplateType $type, string $name, string $language)
    {
        $this->project = $project;
        $this->uuid = $uuid;
        $this->type = $type->value;
        $this->name = $name;
        $this->language = $language;
        $this->template = '';
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Make and change
    // ———————————————————————————————————————————————————————————————————————————————————————

    public static function make(Project $project, Uuid $uuid, TemplateType $type, string $name, string $language): self
    {
        return new self(
            $project,
            $uuid,
            $type,
            $name,
            $language
        );
    }

    public function change(TemplateType $type, string $name, string $language, string $template): void
    {
        $this->type = $type->value;
        $this->name = $name;
        $this->language = $language;
        $this->template = $template;
    }

    public function importMasterTemplate(MasterTemplate $masterTemplate): void
    {
        $this->masterTemplate = $masterTemplate;
        $this->masterTemplateLinkedAt = new \DateTimeImmutable();
    }

    public function resetMasterTemplate(): void
    {
        $this->masterTemplate = null;
        $this->masterTemplateLinkedAt = new \DateTimeImmutable('1970-01-01');
    }

    // ———————————————————————————————————————————————————————————————————————————————————————
    // Getters
    // ———————————————————————————————————————————————————————————————————————————————————————

    public function getType(): TemplateType
    {
        return TemplateType::from($this->type);
    }

    public function getTypeAsString(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getMasterTemplate(): ?MasterTemplate
    {
        return $this->masterTemplate;
    }

    public function getMasterTemplateLinkedAt(): ?\DateTimeImmutable
    {
        return $this->masterTemplateLinkedAt;
    }
}
