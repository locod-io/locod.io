<?php

namespace App\Tests\unit\application\Lodocio\Application\Command\Project;

use App\Lodocio\Application\Command\Project\AddDocProject\AddDocProject;
use PHPUnit\Framework\TestCase;

class AddDocProjectTest extends TestCase
{
    public function testHydrateFromJson()
    {
        $json = (object)[
            'organisationId' => 1,
            'projectId' => 2,
            'name' => 'Test Project',
        ];

        $addDocProject = AddDocProject::hydrateFromJson($json);

        $this->assertInstanceOf(AddDocProject::class, $addDocProject);
        $this->assertEquals(1, $addDocProject->getOrganisationId());
        $this->assertEquals(2, $addDocProject->getProjectId());
        $this->assertEquals('Test Project', $addDocProject->getName());
    }

}