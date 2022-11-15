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

use App\Locodio\Domain\Model\Common\ChecksumEntity;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class ChecksumEntityTest extends TestCase
{
    public function testChecksumEntity(): void
    {
        $checksumEntity = new class () {
            use ChecksumEntity;

            private string $firstname = 'firstname';
            private string $lastname = 'lastname';
            private \DateTime $createdAt;
            private \DateTime $updatedAt;

            public function __construct()
            {
                $this->createdAt = new \DateTime();
                $this->updatedAt = new \DateTime();
            }
        };
        $contentString = hash('sha256', $_ENV['APP_SECRET']);
        $contentString .= 'firstname';
        $contentString .= 'lastname';
        $checksum = md5($contentString);
        $checksumEntity->setChecksum();
        Assert::assertEquals($checksum, $checksumEntity->getChecksum());
    }
}
