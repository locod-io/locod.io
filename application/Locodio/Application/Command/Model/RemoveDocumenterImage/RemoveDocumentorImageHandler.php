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

namespace App\Locodio\Application\Command\Model\RemoveDocumenterImage;

use App\Locodio\Application\Command\Model\ModelFinalChecker;
use App\Locodio\Domain\Model\Model\DocumentorRepository;

class RemoveDocumentorImageHandler
{
    public function __construct(
        protected DocumentorRepository $documentorRepo,
    ) {
    }

    public function go(RemoveDocumentorImage $command): bool
    {
        $documentor = $this->documentorRepo->getById($command->getDocumentorId());
        if (ModelFinalChecker::isFinalState($documentor)) {
            return false;
        }
        $documentor->setImage('');
        $this->documentorRepo->save($documentor);
        return true;
    }
}
