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

namespace App\Locodio\Application\Query\Model;

use App\Locodio\Application\Query\Model\Readmodel\GeneratedCodeRM;
use App\Locodio\Application\Query\Model\Readmodel\TemplateRM;
use App\Locodio\Domain\Model\Model\CommandRepository;
use App\Locodio\Domain\Model\Model\DomainModelRepository;
use App\Locodio\Domain\Model\Model\EnumRepository;
use App\Locodio\Domain\Model\Model\QueryRepository;
use App\Locodio\Domain\Model\Model\TemplateRepository;
use App\Locodio\Domain\Model\Model\TemplateType;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Twig\Environment;

use Twig\Extra\String\StringExtension;
use Twig\Loader\ArrayLoader;

class GetTemplate
{
    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(
        protected TemplateRepository    $templateRepo,
        protected DomainModelRepository $domainModelRepo,
        protected EnumRepository        $enumRepo,
        protected QueryRepository       $queryRepo,
        protected CommandRepository     $commandRepo
    ) {
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Queries
    // ——————————————————————————————————————————————————————————————————————————

    public function ById(int $id): TemplateRM
    {
        $model = $this->templateRepo->getById($id);
        return TemplateRM::hydrateFromModel($model, true);
    }

    public function GenerateBySubjectId(int $id, int $subjectId): GeneratedCodeRM
    {
        $template = $this->templateRepo->getById($id);
        switch ($template->getType()) {
            case TemplateType::DOMAIN_MODEL:
                $subjectCode = 'model';
                $GetDomainModel = new GetDomainModel($this->domainModelRepo);
                $subject = json_decode(json_encode($GetDomainModel->ById($subjectId)));
                break;
            case TemplateType::ENUM:
                $subjectCode = 'enum';
                $GetEnum = new GetEnum($this->enumRepo);
                $subject = json_decode(json_encode($GetEnum->ById($subjectId)));
                break;
            case TemplateType::QUERY:
                $subjectCode = 'model';
                $GetQuery = new GetQuery($this->queryRepo);
                $subject = json_decode(json_encode($GetQuery->ById($subjectId)));
                break;
            case TemplateType::COMMAND:
                $subjectCode = 'model';
                $GetCommand = new GetCommand($this->commandRepo);
                $subject = json_decode(json_encode($GetCommand->ById($subjectId)));
                break;
        }
        $loader = new ArrayLoader(['the template' => $template->getTemplate()]);
        $twig = new Environment($loader);
        $twig->addExtension(new StringExtension());
        try {
            $result = new GeneratedCodeRM(trim($twig->render('the template', [$subjectCode => $subject])));
        } catch (\Exception $exception) {
            $result = new GeneratedCodeRM(
                '',
                false,
                $exception->getMessage(),
            );
        }

        return $result;
    }
}
