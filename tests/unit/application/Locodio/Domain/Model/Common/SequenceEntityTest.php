<?php

namespace App\Tests\unit\application\Locodio\Domain\Model\Common;

use App\Locodio\Domain\Model\Common\SequenceEntity;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class SequenceEntityTest extends TestCase
{
    public function testSequenceTrait(): void
    {
        $sequenceEntity = new class () {
            use SequenceEntity;
        };
        $sequenceEntity->setSequence(1);
        Assert::assertEquals(1, $sequenceEntity->getSequence());
    }
}
