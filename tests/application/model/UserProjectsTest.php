<?php
require_once('AbstractDatabaseTestCase.php');
class UserProjectsTest extends AbstractDatabaseTestCase
{
    public $projects;

    public function setUp()
    {
        parent::setUp();

        $this->projects = User_Model_Projects::getInstance();
    }

    public function testAbstractMethods()
    {
        $this->projects->setUserId(USER_JOHN);
        $project = $this->projects->findByProjectId(PROJECT_WEBSITE);

        $this->assertEquals(PROJECT_WEBSITE, $project->getId());
        $this->assertEquals($project->title, $project->getName());
    }

    /**
     * @expectedException Exception
     */
    public function testFetchByDateOrActiveException()
    {
        // no user ID is set
        $this->projects->setUserId(null);
        $this->projects->fetchByDateOrActive(DATE_1);
    }

    public function testFetchByDateOrActiveOnDate1_activeWithJobs()
    {
        $this->projects->setUserId(USER_JOHN);
        $projectSet = $this->projects->fetchByDateOrActive(DATE_1);

        // expecting results
        $this->assertTrue(count($projectSet) > 0);

        $expectedProjects = array(PROJECT_WEBSITE, PROJECT_ECOMMERCE);
        foreach ($projectSet as $project) {
            $this->assertTrue(in_array($project->id, $expectedProjects));
        }
    }

    public function testFetchByDateOrActive_inactiveWithJobs()
    {
        $this->projects->setUserId(USER_JEN);
        $projectSet = $this->projects->fetchByDateOrActive(DATE_1);

        // expecting results
        $this->assertEquals(1, count($projectSet));

        $this->assertEquals($projectSet->current()->id, PROJECT_WEBSITE);
    }

    public function testFetchByDateOrActive_activeWithNoJobs()
    {
        $this->projects->setUserId(USER_JOHN);
        $projectSet = $this->projects->fetchByDateOrActive(DATE_2);

        // expecting results
        $this->assertTrue(count($projectSet) > 0);

        $expectedProjects = array(PROJECT_WEBSITE, PROJECT_ECOMMERCE);
        foreach ($projectSet as $project) {
            $this->assertTrue(in_array($project->id, $expectedProjects));
        }
    }

    public function testFetchByDateOrActive_inactiveWithNoJobs()
    {
        $this->projects->setUserId(USER_JEN);
        $projectSet = $this->projects->fetchByDateOrActive(DATE_2);

        // expecting results
        $this->assertEquals(0, count($projectSet));
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

    public function testProjectFetchJobs()
    {
        $this->projects->setUserId(USER_JOHN);
        $project = $this->projects->findByProjectId(PROJECT_WEBSITE);
        $jobs    = $project->fetchJobs(DATE_1);

        $this->assertTrue(count($jobs) > 0);
        foreach ($jobs as $job) {
            // make sure job belongs to user and project and date
            $this->assertEquals(USER_JOHN, $job->user_id);
            $this->assertEquals(PROJECT_WEBSITE, $job->project_id);
            $this->assertEquals(DATE_1, $job->date);
        }

        $noJobs = $project->fetchJobs(DATE_2);
        $this->assertEquals(0, count($noJobs));
    }

    public function testProjectCreation()
    {
        $data = array(
            'user_id'     => USER_JOHN,
            'title'       => 'New Project creation',
            'description' => 'This the new project description',
            'note'        => 'This is my personal project note',
        );

        $userProject = User_Model_Project::create($data);
        $project     = App_Model_Projects::getInstance()->findByTitle($data['title']);

        $this->assertInstanceOf('App_Model_Project', $project);
        $this->assertEquals($data['title'], $project->title);
        $this->assertEquals($data['description'], $project->description);

        $this->assertEquals($project->id, $userProject->project_id);
        $this->assertEquals($data['user_id'], $userProject->user_id);
        $this->assertEquals($data['note'], $userProject->note);
    }

    public function testCanDelete()
    {
        $this->projects->setUserId(USER_JOHN);
        $projectWithJobs = $this->projects->findByProjectId(PROJECT_WEBSITE);
        $this->assertFalse($projectWithJobs->canDelete());

        $userProjectNoJobs = $this->projects->findByProjectId(PROJECT_SERVICES);
        $this->assertTrue($userProjectNoJobs->canDelete());

        $this->projects->setUserId(USER_JACK);
        $userInactiveProjectNoJobs = $this->projects->findByProjectId(PROJECT_SERVICES);
        $this->assertTrue($userInactiveProjectNoJobs->canDelete());
    }

    public function testDelete()
    {
        $projects = App_Model_Projects::getInstance();

        // Delete USER_JOHN's PROJECT_SERVICES
        $this->projects->setUserId(USER_JOHN);
        $project = $this->projects->findByProjectId(PROJECT_SERVICES);
        $project->delete();

        // check that USER_JOHN's PROJECT_SERVICES is deleted
        $this->assertTrue(is_null($this->projects->findByProjectId(PROJECT_SERVICES)));

        // PROJECT_SERVICES should still exist
        $this->assertInstanceOf('App_Model_Project', $projects->find(PROJECT_SERVICES));

        // Delete USER_JACK's PROJECT_SERVICES
        $this->projects->setUserId(USER_JACK);
        $project = $this->projects->findByProjectId(PROJECT_SERVICES);
        $project->delete();

        // PROJECT_SERVICES be deleted
        $this->assertNull($projects->find(PROJECT_SERVICES));
    }
}