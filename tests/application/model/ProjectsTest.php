<?php
require_once('AbstractDatabaseTestCase.php');
class ProjectsTest extends AbstractDatabaseTestCase
{
    const PROJECT_1_ID          = 1;
    const PROJECT_2_ID          = 2;
    const PROJECT_3_ID          = 3;

    const PROJECT_1_TOTAL_HOURS = 16;
    const PROJECT_2_TOTAL_HOURS = 8;
    const PROJECT_3_TOTAL_HOURS = 0;

    public $projects;

    protected $_fixtureDataSet = 'userProjectsTest.xml';

    public function setUp()
    {
        parent::setUp();

        $this->projects = App_Model_Projects::getInstance();
    }

    public function testTotalHours()
    {
        $project = $this->projects->find(self::PROJECT_1_ID);
        $this->assertEquals(self::PROJECT_1_TOTAL_HOURS, $project->project_total_hours);

        $project = $this->projects->find(self::PROJECT_2_ID);
        $this->assertEquals(self::PROJECT_2_TOTAL_HOURS, $project->project_total_hours);

        $project = $this->projects->find(self::PROJECT_3_ID);
        $this->assertEquals(self::PROJECT_3_TOTAL_HOURS, $project->project_total_hours);
    }

    public function testCanDelete()
    {
        $project = $this->projects->find(self::PROJECT_1_ID);
        $this->assertFalse($project->canDelete());

        $project = $this->projects->find(self::PROJECT_2_ID);
        $this->assertFalse($project->canDelete());

        $project = $this->projects->find(self::PROJECT_3_ID);
        $this->assertTrue($project->canDelete());
    }
}