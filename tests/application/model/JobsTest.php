<?php
require_once('AbstractDatabaseTestCase.php');
class JobsTest extends AbstractDatabaseTestCase
{
    const PROJECT_ID     = 1;
    const ALT_PROJECT_ID = 2;
    const TASK_ID        = 1;
    const ALT_TASK_ID    = 2;
    const USER_ID        = 1;
    const DATE           = '2011-01-01';

    const TOTAL_JOBS           = 5;
    const TOTAL_PROJECT_1_JOBS = 4;

    protected $_fixtureDataSet = 'jobsDataSet.xml';

    public $jobs;

    public function setUp()
    {
        parent::setUp();
        $this->jobs = App_Model_Jobs::getInstance();

        $this->jobs->setDate(self::DATE);
        $this->jobs->setUserId(self::USER_ID);
        $this->jobs->setProjectId(self::PROJECT_ID);
    }

    public function testFetchAllByUserAndProject()
    {
        $jobs = $this->jobs->fetchAll();

        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS, count($jobs));

        return $jobs;
    }

    public function testCreateJob()
    {
        $this->jobs->add(self::TASK_ID, 19.5, 20);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS + 1, count($this->jobs->fetchAll()));
    }

    public function testAppendingTimeToJob()
    {
        $this->jobs->add(self::TASK_ID, 17, 17.5);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 17.5);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS, count($this->jobs->fetchAll()));
    }

    public function testPrependingTimeToJob()
    {
        $this->jobs->add(self::TASK_ID, 12, 13);

        // find job affect
        $job = $this->jobs->findByStartTime(12);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS, count($this->jobs->fetchAll()));
    }

    public function testPrependOverlappingTimeToJob()
    {
        $this->jobs->add(self::TASK_ID, 12, 13.25);

        // find job affect
        $job = $this->jobs->findByStartTime(12);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS, count($this->jobs->fetchAll()));
    }

    public function testCombineJobs()
    {
        $this->jobs->add(self::TASK_ID, 17, 18);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 18.50);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS - 1, count($this->jobs->fetchAll()));
    }

    public function testEngulfJobs()
    {
        $this->jobs->add(self::TASK_ID, 12, 20);

        // find job affect
        $job = $this->jobs->findByStartTime(12);

        $this->assertEquals($job->stop_time, 20);
        $this->assertEquals(1, count($this->_fetchFromAllProjects()));
    }

    public function testAppendingOtherTask()
    {
        $this->jobs->add(self::ALT_TASK_ID, 17, 17.5);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 17.0);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS + 1, count($this->jobs->fetchAll()));
    }

    public function testPrependingOtherTask()
    {
        $this->jobs->add(self::ALT_TASK_ID, 12, 13);

        // find job affect
        $job = $this->jobs->findByStartTime(13);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS + 1, count($this->jobs->fetchAll()));
    }

    public function testPrependingOtherTaskOverlapping()
    {
        $this->jobs->add(self::ALT_TASK_ID, 12, 13.25);

        // find job affect
        $job = $this->jobs->findByStartTime(13.25);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS + 1, count($this->jobs->fetchAll()));
    }

    public function testAppendingOtherTaskOverlapping()
    {
        $this->jobs->add(self::ALT_TASK_ID, 16.75, 17.5);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 16.75);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS + 1, count($this->jobs->fetchAll()));
    }

    public function testSeparateExistingJob()
    {
        $this->jobs->add(self::ALT_TASK_ID, 15.25, 15.75);

        $oldJobPart1 = $this->jobs->findByStartTime(13);
        $this->assertEquals($oldJobPart1->stop_time, 15.25);
        $this->assertEquals($oldJobPart1->task_id, self::TASK_ID);

        $oldJobPart2 = $this->jobs->findByStartTime(15.75);
        $this->assertEquals($oldJobPart2->stop_time, 16.25);
        $this->assertEquals($oldJobPart2->task_id, self::TASK_ID);

        $newJob = $this->jobs->findByStartTime(15.25);
        $this->assertEquals($newJob->stop_time, 15.75);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS + 2, count($this->jobs->fetchAll()));
    }

    public function testOtherTaskOverwritingJob()
    {
        $this->jobs->add(self::ALT_TASK_ID, 18, 19);

        // find job affect
        $job = $this->jobs->findByStartTime(18);

        $this->assertEquals($job->stop_time, 19);
        $this->assertEquals($job->task_id, self::ALT_TASK_ID);
        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS, count($this->jobs->fetchAll()));
    }

    public function testOtherOverwritesMultipleJobs()
    {
        $this->jobs->add(self::ALT_TASK_ID, 14.5, 18.75);

        $oldJob1 = $this->jobs->findByStartTime(13);
        $this->assertEquals($oldJob1->stop_time, 14.5);
        $this->assertEquals($oldJob1->task_id, self::TASK_ID);

        $job = $this->jobs->findByStartTime(14.5);
        $this->assertEquals($job->stop_time, 18.75);
        $this->assertEquals($job->task_id, self::ALT_TASK_ID);

        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS - 2, count($this->jobs->fetchAll()));

        $this->jobs->setProjectId(self::ALT_PROJECT_ID);
        $oldJob2 = $this->jobs->findByStartTime(18.75);
        $this->assertEquals($oldJob2->stop_time, 19);
        $this->assertEquals($oldJob2->task_id, self::TASK_ID);

        $this->assertEquals(self::TOTAL_JOBS - 2, count($this->_fetchFromAllProjects()));
    }

    public function testOtherJobOverwriteAndCombine()
    {
        $this->jobs->add(self::ALT_TASK_ID, 16.50, 18.75);

        $oldJob1 = $this->jobs->findByStartTime(13);
        $this->assertEquals($oldJob1->stop_time, 16.25);
        $this->assertEquals($oldJob1->task_id, self::TASK_ID);

        $job = $this->jobs->findByStartTime(16.25);
        $this->assertEquals($job->stop_time, 18.75);
        $this->assertEquals($job->task_id, self::ALT_TASK_ID);

        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS - 2, count($this->jobs->fetchAll()));

        $this->jobs->setProjectId(self::ALT_PROJECT_ID);
        $oldJob2 = $this->jobs->findByStartTime(18.75);
        $this->assertEquals($oldJob2->stop_time, 19);
        $this->assertEquals($oldJob2->task_id, self::TASK_ID);
    }

    public function testDeleteMultipleJobs()
    {
        $this->jobs->remove(13, 17.25);

        // get remaining jobs
        $jobs = $this->jobs->fetchAll();

        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS - 3, count($jobs));
        $this->assertEquals($jobs->current()->start_time, 18.00);
    }

    public function testDeleteBeginningOfJob()
    {
        $this->jobs->remove(12.75, 13.5);

        // get remaining jobs
        $job = $this->jobs->findByStartTime(13.5);

        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS, count($this->jobs->fetchAll()));
        $this->assertEquals($job->stop_time, 16.25);
    }

    public function testDeleteEndOfJob()
    {
        $this->jobs->remove(15.75, 16.25);

        // get remaining jobs
        $job = $this->jobs->findByStartTime(13);

        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS, count($this->jobs->fetchAll()));
        $this->assertEquals($job->stop_time, 15.75);
    }

    public function testDeleteAndSplitJob()
    {
        $this->jobs->remove(14, 15);

        $jobPart1 = $this->jobs->findByStartTime(13);
        $jobPart2 = $this->jobs->findByStartTime(15);

        $this->assertEquals(self::TOTAL_PROJECT_1_JOBS + 1, count($this->jobs->fetchAll()));
        $this->assertEquals($jobPart1->stop_time, 14);
        $this->assertEquals($jobPart2->stop_time, 16.25);
    }

    public function testDeleteProject1()
    {
        $this->jobs->remove(13, 19);

        $this->assertEquals(0, count($this->jobs->fetchAll()));

        // make sure project 2 is not affected
        $this->jobs->setProjectId(self::ALT_PROJECT_ID);
        $this->assertEquals(1, count($this->jobs->fetchAll()));
    }

    private function _fetchFromAllProjects()
    {
        // search through entire all project jobs
        $this->jobs->setProjectId(null);

        return $this->jobs->fetchAll();
    }
}