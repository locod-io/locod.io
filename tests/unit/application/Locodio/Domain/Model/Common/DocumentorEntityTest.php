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

use App\Locodio\Domain\Model\Common\DocumentorEntity;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class DocumentorEntityTest extends TestCase
{
    public function testSequenceTrait(): void
    {
        $documentorEntity = new class () {
            use DocumentorEntity;
        };
        $documentorEntity->document(ModelFactory::makeDocumentor(DocumentorType::MODULE));
        Assert::assertInstanceOf(Documentor::class, $documentorEntity->getDocumentor());
        Assert::assertEquals(DocumentorType::MODULE, $documentorEntity->getDocumentor()->getType());
        Assert::assertEquals('documentor', $documentorEntity->getDocumentor()->getDescription());
    }
}
