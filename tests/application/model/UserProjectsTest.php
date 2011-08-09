<?php
require_once('AbstractDatabaseTestCase.php');
class UserProjectsTest extends AbstractDatabaseTestCase
{
    const DATE_1         = '1982-03-18';
    const DATE_2         = '1982-03-17';

    const TEST_USER_ID   = 1;

    const TOTAL_PROJECTS           = 2;
    const TOTAL_PROJECTS_ON_DATE_1 = 1;
    const TOTAL_PROJECTS_ON_DATE_2 = 1;

    public $projects;

    protected $_fixtureDataSet = 'userProjectsTest.xml';

    public function setUp()
    {
        parent::setUp();

        $this->projects = User_Model_Projects::getInstance();
        $this->projects->setUserId(self::TEST_USER_ID);
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
}