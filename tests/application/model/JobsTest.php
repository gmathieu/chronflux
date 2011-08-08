<?php
require_once('AbstractDatabaseTestCase.php');
class JobsTest extends AbstractDatabaseTestCase
{
    const PROJECT_ID  = 1;
    const TASK_ID     = 1;
    const ALT_TASK_ID = 2;
    const USER_ID     = 1;
    const DATE        = '2011-01-01';

    const TOTAL_JOBS = 4;

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

        $this->assertEquals(self::TOTAL_JOBS, count($jobs));

        return $jobs;
    }

    public function testCreateJob()
    {
        $this->jobs->addTime(self::TASK_ID, 19.5, 20);
        $this->assertEquals(self::TOTAL_JOBS + 1, count($this->jobs->fetchAll()));
    }

    public function testAppendingTimeToJob()
    {
        $this->jobs->addTime(self::TASK_ID, 17, 17.5);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 17.5);
        $this->assertEquals(self::TOTAL_JOBS, count($this->jobs->fetchAll()));
    }

    public function testPrependingTimeToJob()
    {
        $this->jobs->addTime(self::TASK_ID, 12, 13);

        // find job affect
        $job = $this->jobs->findByStartTime(12);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(self::TOTAL_JOBS, count($this->jobs->fetchAll()));
    }

    public function testPrependOverlappingTimeToJob()
    {
        $this->jobs->addTime(self::TASK_ID, 12, 13.25);

        // find job affect
        $job = $this->jobs->findByStartTime(12);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(self::TOTAL_JOBS, count($this->jobs->fetchAll()));
    }

    public function testCombineJobs()
    {
        $this->jobs->addTime(self::TASK_ID, 17, 18);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 19);
        $this->assertEquals(self::TOTAL_JOBS - 1, count($this->jobs->fetchAll()));
    }

    public function testEngulfJobs()
    {
        $this->jobs->addTime(self::TASK_ID, 12, 20);

        // find job affect
        $job = $this->jobs->findByStartTime(12);

        $this->assertEquals($job->stop_time, 20);
        $this->assertEquals(1, count($this->jobs->fetchAll()));
    }

    public function testAppendingOtherTask()
    {
        $this->jobs->addTime(self::ALT_TASK_ID, 17, 17.5);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 17.0);
        $this->assertEquals(self::TOTAL_JOBS + 1, count($this->jobs->fetchAll()));
    }

    public function testPrependingOtherTask()
    {
        $this->jobs->addTime(self::ALT_TASK_ID, 12, 13);

        // find job affect
        $job = $this->jobs->findByStartTime(13);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(self::TOTAL_JOBS + 1, count($this->jobs->fetchAll()));
    }

    public function testPrependingOtherTaskOverlapping()
    {
        $this->jobs->addTime(self::ALT_TASK_ID, 12, 13.25);

        // find job affect
        $job = $this->jobs->findByStartTime(13.25);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(self::TOTAL_JOBS + 1, count($this->jobs->fetchAll()));
    }

    public function testAppendingOtherTaskOverlapping()
    {
        $this->jobs->addTime(self::ALT_TASK_ID, 16.75, 17.5);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 16.75);
        $this->assertEquals(self::TOTAL_JOBS + 1, count($this->jobs->fetchAll()));
    }

    public function testSeparateExistingJob()
    {
        $this->jobs->addTime(self::ALT_TASK_ID, 15.25, 15.75);

        $oldJobPart1 = $this->jobs->findByStartTime(13);
        $this->assertEquals($oldJobPart1->stop_time, 15.25);
        $this->assertEquals($oldJobPart1->task_id, self::TASK_ID);

        $oldJobPart2 = $this->jobs->findByStartTime(15.75);
        $this->assertEquals($oldJobPart2->stop_time, 16.25);
        $this->assertEquals($oldJobPart2->task_id, self::TASK_ID);

        $newJob = $this->jobs->findByStartTime(15.25);
        $this->assertEquals($newJob->stop_time, 15.75);
        $this->assertEquals(self::TOTAL_JOBS + 2, count($this->jobs->fetchAll()));
    }

    public function testOtherTaskOverwritingJob()
    {
         $this->jobs->addTime(self::ALT_TASK_ID, 18, 19);

        // find job affect
        $job = $this->jobs->findByStartTime(18);

        $this->assertEquals($job->stop_time, 19);
        $this->assertEquals($job->task_id, self::ALT_TASK_ID);
        $this->assertEquals(self::TOTAL_JOBS, count($this->jobs->fetchAll()));
    }

    public function testOtherOverwritesMultipleJobs()
    {
         $this->jobs->addTime(self::ALT_TASK_ID, 14.5, 18.75);

        $oldJob1 = $this->jobs->findByStartTime(13);
        $this->assertEquals($oldJob1->stop_time, 14.5);
        $this->assertEquals($oldJob1->task_id, self::TASK_ID);

        $oldJob2 = $this->jobs->findByStartTime(18.75);
        $this->assertEquals($oldJob2->stop_time, 19);
        $this->assertEquals($oldJob2->task_id, self::TASK_ID);

        $job = $this->jobs->findByStartTime(14.5);
        $this->assertEquals($job->stop_time, 18.75);
        $this->assertEquals($job->task_id, self::ALT_TASK_ID);

        $this->assertEquals(self::TOTAL_JOBS - 1, count($this->jobs->fetchAll()));
    }

    public function testOtherJobOverwriteAndCombine()
    {
        $this->jobs->addTime(self::ALT_TASK_ID, 16.50, 18.50);

        $oldJob1 = $this->jobs->findByStartTime(13);
        $this->assertEquals($oldJob1->stop_time, 16.25);
        $this->assertEquals($oldJob1->task_id, self::TASK_ID);

        $oldJob2 = $this->jobs->findByStartTime(18.5);
        $this->assertEquals($oldJob2->stop_time, 19);
        $this->assertEquals($oldJob2->task_id, self::TASK_ID);

        $job = $this->jobs->findByStartTime(16.25);
        $this->assertEquals($job->stop_time, 18.50);
        $this->assertEquals($job->task_id, self::ALT_TASK_ID);

        $this->assertEquals(self::TOTAL_JOBS - 1, count($this->jobs->fetchAll()));
    }
}