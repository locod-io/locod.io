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

namespace App\Tests\unit\application\Locodio\Application\Query\User;

use App\Locodio\Application\Query\User\Readmodel\PasswordResetLinkRM;
use App\Locodio\Domain\Model\User\PasswordResetLink;
use App\Tests\unit\application\Locodio\Domain\Model\ModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class PasswordResetLinkReadModelTest extends TestCase
{
    public function testReadModel(): void
    {
        $user = ModelFactory::makeUser();
        $user->identify(1, Uuid::v4());
        $link = PasswordResetLink::make(Uuid::v4(), $user,'signature');
        $link->identify(2, Uuid::fromString('d09deabb-b81e-478e-a18e-5dbaf3f67b28'));
        $readModel = PasswordResetLinkRM::hydrateFromModel($link);
        Assert::assertEquals(1, $readModel->getUserId());
        Assert::assertEquals('signature', $readModel->getCode());
        Assert::assertEquals('d09deabb-b81e-478e-a18e-5dbaf3f67b28', $readModel->getUuid());
        Assert::assertFalse($readModel->isUsed());
        Assert::assertTrue($readModel->isActive());
        Assert::assertNotEmpty($readModel->getCode());
        Assert::assertInstanceOf(\DateTime::class, $readModel->getExpiresAt());
        Assert::assertGreaterThan(new \DateTime(), $readModel->getExpiresAt());
    }
}
