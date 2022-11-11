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

namespace App\Locodio\Application\Command\User\ChangePassword;

use ZxcvbnPhp\Zxcvbn;

class ChangePassword
{
    public function __construct(
        protected int    $userId,
        protected string $password1,
        protected string $password2,
    ) {
    }

    public static function hydrateFromJson(\stdClass $json): self
    {
        return new self($json->userId, $json->password1, $json->password2);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getPassword1(): string
    {
        return $this->password1;
    }

    public function getPassword2(): string
    {
        return $this->password2;
    }

    public function isPasswordValid(): bool
    {
        if ($this->password1 !== $this->password2) {
            return false;
        }
        $zxcvbn = new Zxcvbn();
        $passwordStrength = $zxcvbn->passwordStrength($this->password1);
        if ($passwordStrength['score'] < 3) {
            return false;
        }
        return true;
    }
}
