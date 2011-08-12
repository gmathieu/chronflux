<?php
require_once('AbstractDatabaseTestCase.php');
class ProjectsTest extends AbstractDatabaseTestCase
{
    public $projects;

    public function setUp()
    {
        parent::setUp();

        $this->projects = App_Model_Projects::getInstance();
    }

    public function testCanDelete()
    {
        $project = $this->projects->find(PROJECT_USED);
        $this->assertFalse($project->canDelete());

        $project = $this->projects->find(PROJECT_UNUSED);
        $this->assertTrue($project->canDelete());
    }
}