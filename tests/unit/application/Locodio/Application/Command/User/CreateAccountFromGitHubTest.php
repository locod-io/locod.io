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

namespace App\Tests\unit\application\Locodio\Application\Command\User;

use App\Locodio\Application\Command\User\CreateAccountFromGitHub\CreateAccountFromGitHub;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CreateAccountFromGitHubTest extends TestCase
{

    public function testCommand(): void
    {
        $command = CreateAccountFromGitHub::make(
            name: 'firstname lastname',
            email: 'firstname.lastname@test.com',
            company: 'My company',
        );

        Assert::assertEquals('Firstname', $command->getFirstName());
        Assert::assertEquals('Lastname', $command->getLastName());
        Assert::assertEquals('firstname.lastname@test.com', $command->getEmail());
        Assert::assertEquals('My company', $command->getOrganisation());
    }

    public function testCommandNameFromEmail(): void
    {
        $command = CreateAccountFromGitHub::make(
            name: 'firstname-lastname',
            email: 'firstname.lastname@test.com',
            company: 'My company',
        );

        Assert::assertEquals('Firstname', $command->getFirstName());
        Assert::assertEquals('Lastname', $command->getLastName());
        Assert::assertEquals('firstname.lastname@test.com', $command->getEmail());
        Assert::assertEquals('My company', $command->getOrganisation());
    }

    public function testCommandLongName(): void
    {
        $command = CreateAccountFromGitHub::make(
            name: 'firstname lastname extension',
            email: 'firstname.lastname@test.com',
            company: 'My company',
        );

        Assert::assertEquals('Firstname', $command->getFirstName());
        Assert::assertEquals('Lastname extension', $command->getLastName());
        Assert::assertEquals('firstname.lastname@test.com', $command->getEmail());
        Assert::assertEquals('My company', $command->getOrganisation());
    }

    public function testCommandLongEmail(): void
    {
        $command = CreateAccountFromGitHub::make(
            name: 'firstname-lastname',
            email: 'firstname.lastname.extension@test.com',
            company: 'My company',
        );

        Assert::assertEquals('Firstname', $command->getFirstName());
        Assert::assertEquals('Lastname.extension', $command->getLastName());
        Assert::assertEquals('firstname.lastname.extension@test.com', $command->getEmail());
        Assert::assertEquals('My company', $command->getOrganisation());
    }

    public function testCommandSingleName(): void
    {
        $command = CreateAccountFromGitHub::make(
            name: 'single-name',
            email: 'single-name@test.com',
            company: 'My company',
        );

        Assert::assertEquals('*', $command->getFirstName());
        Assert::assertEquals('single-name', $command->getLastName());
        Assert::assertEquals('single-name@test.com', $command->getEmail());
        Assert::assertEquals('My company', $command->getOrganisation());
    }

}