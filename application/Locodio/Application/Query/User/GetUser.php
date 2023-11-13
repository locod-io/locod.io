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

namespace App\Locodio\Application\Query\User;

use App\Locodio\Application\Query\User\Readmodel\UserRM;
use App\Locodio\Domain\Model\User\UserRepository;
use Assert\Assertion;
use Assert\InvalidArgumentException;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Security\Core\Security;

class GetUser
{
    // ———————————————————————————————————————————————————————————————
    // Constructor
    // ———————————————————————————————————————————————————————————————

    public function __construct(
        protected Security       $security,
        protected UserRepository $userRepo,
    ) {
    }

    // ———————————————————————————————————————————————————————————————
    // Read information
    // ———————————————————————————————————————————————————————————————

    public function FromSession(): UserRM
    {
        $user = $this->security->getUser();
        return UserRM::hydrateFromModel($user, true);
    }

    /**
     * @throws \Exception
     */
    public function ById(int $id): UserRM
    {
        $user = UserRM::hydrateFromModel($this->userRepo->getById($id), true);
        return $user;
    }

    public function CheckByEmail(string $email): ?bool
    {
        try {
            Assertion::email($email);
        } catch (InvalidArgumentException $e) {
            return null;
        }
        try {
            $user = $this->userRepo->getByEmail(trim($email));
            return true;
        } catch (EntityNotFoundException $e) {
            return false;
        }
    }
}
