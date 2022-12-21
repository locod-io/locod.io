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

namespace App\Locodio\Application\Command\Model;

use App\Locodio\Domain\Model\Model\Documentor;

class ModelFinalChecker
{
    public static function isFinalState(?Documentor $documentor): bool
    {
        if (is_null($documentor)) {
            return false;
        } elseif ($documentor->getStatus()->isFinal()) {
            return true;
        }
        return false;
    }
}
