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

use App\Locodio\Application\Query\Model\Readmodel\GeneratedCodeRM;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class GeneratedCodeReadModelTest extends TestCase
{
    public function testReadModel(): void
    {
        $readModel = new GeneratedCodeRM('code');
        $result = json_decode(json_encode($readModel));
        Assert::assertEquals('code', $result->code);
    }
}
