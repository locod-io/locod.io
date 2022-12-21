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

namespace App\Tests\unit\application\Locodio\Application\Query\Model;

use App\Locodio\Application\Query\Model\Readmodel\DocumentorRM;
use App\Locodio\Domain\Model\Model\Documentor;
use App\Locodio\Domain\Model\Model\DocumentorType;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class DocumentorReadModelTest extends TestCase
{
    private Documentor $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = Documentor::make(
            Uuid::v4(),
            DocumentorType::MODULE,
            'description',
            ModelFactory::makeModelStatus()
        );
        $this->model->identify(1, Uuid::fromString('97860240-988d-4de8-a0a9-1cbf84e2fe18'));
    }

    public function testReadModel(): void
    {
        $readModel = DocumentorRM::hydrateFromModel($this->model, true);
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals(1, $result->id);
        Assert::assertEquals('97860240-988d-4de8-a0a9-1cbf84e2fe18', $result->uuid);
        Assert::assertEquals('description', $result->description);
        Assert::assertEquals('status', $result->status->name);
    }
}
