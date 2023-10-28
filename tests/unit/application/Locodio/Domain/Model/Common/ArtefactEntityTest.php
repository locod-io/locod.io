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

namespace App\Tests\unit\application\Locodio\Domain\Model\Common;

use App\Locodio\Domain\Model\Common\ArtefactEntity;
use PHPUnit\Framework\TestCase;

final class ArtefactEntityTest extends TestCase
{
    public function testGetArtefactId()
    {
        $artefactEntity = new class () {
            use ArtefactEntity;
        };

        $artefactEntity->setArtefactId(42);
        $this->assertEquals(42, $artefactEntity->getArtefactId());
    }

    public function testSetArtefactId()
    {
        $artefactEntity = new class () {
            use ArtefactEntity;
        };

        $artefactEntity->setArtefactId(42);
        $this->assertEquals(42, $artefactEntity->getArtefactId());
    }
}
