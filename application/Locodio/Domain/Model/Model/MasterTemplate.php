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
use App\Locodio\Domain\Model\User\User;
use Assert\Assertion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation as Audit;
use Symfony\Component\Uid\Uuid;

/**
 * @Audit\Auditable()
 */
#[ORM\Entity(repositoryClass: \App\Locodio\Infrastructure\Database\MasterTemplateRepository::class)]
class MasterTemplate
{
    // —————————————————————————————————————————————————————————
    // Properties
    // —————————————————————————————————————————————————————————

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

    #[ORM\Column(options: ["default" => 0])]
    private bool $isPublic = false;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column]
    private array $tags = [];

    // —————————————————————————————————————————————————————————
    // Relations
    // —————————————————————————————————————————————————————————

    #[ORM\ManyToOne(targetEntity: "App\Locodio\Domain\Model\User\User", fetch: "EXTRA_LAZY", inversedBy: "masterTemplates")]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\OneToMany(mappedBy: "masterTemplate", targetEntity: "App\Locodio\Domain\Model\Model\Template", fetch: "EXTRA_LAZY")]
    #[ORM\OrderBy(['sequence' => 'ASC'])]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $templates;

    // —————————————————————————————————————————————————————————
    // Constructor
    // —————————————————————————————————————————————————————————

    private function __construct(
        User         $user,
        Uuid         $uuid,
        TemplateType $type,
        string       $name,
        string       $language
    ) {
        $this->user = $user;
        $this->uuid = $uuid;
        $this->type = $type->value;
        $this->name = $name;
        $this->language = $language;
        $this->template = '';
        $this->isPublic = false;
        $this->description = '';
        $this->templates = new ArrayCollection();
        $this->tags = [];
    }

    // —————————————————————————————————————————————————————————
    // Make and change
    // —————————————————————————————————————————————————————————

    public static function make(
        User         $user,
        Uuid         $uuid,
        TemplateType $type,
        string       $name,
        string       $language
    ): self {
        Assertion::notEmpty($name);
        Assertion::notEmpty($language);
        return new self(
            $user,
            $uuid,
            $type,
            $name,
            $language
        );
    }

    public function change(
        TemplateType $type,
        string       $name,
        string       $language,
        string       $template,
        bool         $isPublic,
        string       $description,
        array        $tags,
    ): void {
        Assertion::notEmpty($language);
        Assertion::notEmpty($template);
        $this->type = $type->value;
        $this->name = $name;
        $this->language = $language;
        $this->template = $template;
        $this->isPublic = $isPublic;
        $this->description = $description;
        $this->tags = $tags;
    }

    public function changeTemplateContents(string $template): void
    {
        Assertion::notEmpty($template);
        $this->template = $template;
    }

    public function importTemplate(Template $template): void
    {
        $this->templates->add($template);
    }

    // —————————————————————————————————————————————————————————
    // Getters
    // —————————————————————————————————————————————————————————

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

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
