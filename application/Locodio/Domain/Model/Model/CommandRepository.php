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

use App\Locodio\Domain\Model\Organisation\Project;
use Symfony\Component\Uid\Uuid;

interface CommandRepository extends ArtefactRepositoryInterface
{
    public function save(Command $model): ?int;

    public function delete(Command $model): bool;

    public function getById(int $id): Command;

    public function getByUuid(string $uuid): Command;

    public function getByDocumentor(Documentor $documentor): Command;

    /** @return Command[] */
    public function getByProject(Project $project): array;

    /** @return Command[] */
    public function getByDomainModel(DomainModel $domainModel): array;
}
