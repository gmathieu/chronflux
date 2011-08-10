<?php
require_once('AbstractDatabaseTestCase.php');
class UserProjectsTest extends AbstractDatabaseTestCase
{
    const DATE_1         = '1982-03-18';
    const DATE_2         = '1982-03-17';

    const TEST_USER_ID     = 1;
    const TEST_PROJECT_ID  = 1;
    const EMPTY_PROJECT_ID = 3;

    const TOTAL_PROJECTS           = 2;
    const TOTAL_PROJECTS_ON_DATE_1 = 1;
    const TOTAL_PROJECTS_ON_DATE_2 = 1;

    const TOTAL_USER_1_PROJECT_HOURS = 8;

    public $projects;

    protected $_fixtureDataSet = 'userProjectsTest.xml';

    public function setUp()
    {
        parent::setUp();

        $this->projects = User_Model_Projects::getInstance();
        $this->projects->setUserId(self::TEST_USER_ID);
    }

    /**
     * @expectedException Exception
     */
    public function testFetchByDateOrActiveException()
    {
        $this->projects->setUserId(null);
        $this->projects->fetchByDateOrActive(self::DATE_1);
    }

    public function testFetchByDateOrActiveOnDate1()
    {
        $projectSet = $this->projects->fetchByDateOrActive(self::DATE_1);
        $this->assertEquals(self::TOTAL_PROJECTS_ON_DATE_1, count($projectSet));
    }

    public function testFetchByDateOrActiveOnDate2()
    {
        $projectSet = $this->projects->fetchByDateOrActive(self::DATE_2);
        $this->assertEquals(self::TOTAL_PROJECTS, count($projectSet));
    }

    public function testFetchByDateOrActiveWithNoActive()
    {
        // update all projects to inactive
        $this->projects->adapter->query("update user_projects set `active` = 0");

        $projectSet = $this->projects->fetchByDateOrActive(self::DATE_2);
        $this->assertEquals(self::TOTAL_PROJECTS_ON_DATE_2, count($projectSet));
        $this->assertInstanceOf('User_Model_Project', $projectSet->current());

        $projectSet = $this->projects->fetchByDateOrActive(self::DATE_1);
        $this->assertEquals(self::TOTAL_PROJECTS_ON_DATE_1, count($projectSet));
        $this->assertInstanceOf('User_Model_Project', $projectSet->current());
    }

    public function testProjectActivation()
    {
        $firstInactive = $this->projects->findByActive(0);
        $firstInactive->activate();
        $this->assertTrue((boolean)$firstInactive->active);

        $firstActive = $this->projects->findByActive(1);
        $firstActive->deactivate();
        $this->assertFalse((boolean)$firstActive->active);
    }

    public function testTotalHours()
    {
        $project = $this->projects->find(self::TEST_USER_ID, self::TEST_PROJECT_ID);
        $this->assertEquals(self::TOTAL_USER_1_PROJECT_HOURS, $project->user_project_total_hours);
    }

    public function testCanDelete()
    {
        $project = $this->projects->find(self::TEST_USER_ID, self::TEST_PROJECT_ID);
        $this->assertFalse($project->canDelete());

        $project = $this->projects->find(self::TEST_USER_ID, self::EMPTY_PROJECT_ID);
        $this->assertTrue($project->canDelete());
    }

    public function testProjectFetchJobs()
    {
        $projectSet = $this->projects->fetchByDateOrActive(self::DATE_1);
        $project = $projectSet->current();

        $jobs = $project->fetchJobs(self::DATE_1);

        $this->assertEquals(2, count($jobs));
    }

    public function testProjectCreation()
    {
        $data = array(
            'user_id'     => 1,
            'name'        => 'New Project creation',
            'description' => 'This the new project description',
            'note'        => 'This is my personal project note',
        );

        $userProject = User_Model_Project::create($data);
        $project     = App_Model_Projects::getInstance()->findByName($data['name']);

        $this->assertInstanceOf('App_Model_Project', $project);
        $this->assertFalse(is_null($userProject->project_id));
        $this->assertEquals($userProject->user_id, $data['user_id']);
        $this->assertEquals($userProject->name, $data['name']);
        $this->assertEquals($userProject->description, $data['description']);
        $this->assertEquals($userProject->note, $data['note']);
    }
}