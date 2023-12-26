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

namespace App\Locodio\Infrastructure\CLI;

use App\Locodio\Domain\Model\Organisation\Organisation;
use App\Locodio\Domain\Model\Organisation\OrganisationUser;
use App\Locodio\Domain\Model\User\User;
use App\Locodio\Domain\Model\User\UserRole;
use App\Locodio\Infrastructure\Database\OrganisationRepository;
use App\Locodio\Infrastructure\Database\OrganisationUserRepository;
use App\Locodio\Infrastructure\Database\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncOrganisationUsers extends Command
{
    private readonly EntityManagerInterface $entityManager;
    private readonly UserRepository $userRepository;
    private readonly OrganisationRepository $organisationRepository;
    private readonly OrganisationUserRepository $organisationUserRepository;

    // ——————————————————————————————————————————————————————————————————————————
    // Constructor
    // ——————————————————————————————————————————————————————————————————————————

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('locodio:sync:organisation-users');
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->organisationRepository = $entityManager->getRepository(Organisation::class);
        $this->organisationUserRepository = $entityManager->getRepository(OrganisationUser::class);
    }

    // ——————————————————————————————————————————————————————————————————————————
    // Executor
    // ——————————————————————————————————————————————————————————————————————————

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $organisations = $this->organisationRepository->getAll();
        /** @var Organisation $organisation */
        foreach ($organisations as $organisation) {
            $users = $organisation->getUsers();
            /** @var User $user */
            foreach ($users as $user) {
                $organisationUser = $this->organisationUserRepository->findByUserAndOrganisation($user, $organisation);
                if (true === is_null($organisationUser)) {
                    $organisationUser = OrganisationUser::make(
                        uuid: $this->organisationUserRepository->nextIdentity(),
                        user: $user,
                        organisation: $organisation
                    );
                    $organisationUser->setRoles([UserRole::ROLE_ORGANISATION_ADMIN->value, UserRole::ROLE_ORGANISATION_USER->value]);
                    $this->organisationUserRepository->save($organisationUser);
                    $this->entityManager->flush();
                    $output->writeln('"' . $user->getEmail() . '" is coupled to ' . $organisation->getName());
                }
            }
        }
        return 0;
    }
}
