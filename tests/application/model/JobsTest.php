<?php
require_once('AbstractDatabaseTestCase.php');
class JobsTest extends AbstractDatabaseTestCase
{
    public $jobs;

    public function setUp()
    {
        parent::setUp();

        $this->jobs = App_Model_Jobs::getInstance();
        $this->jobs->setDate(DATE_1);
        $this->jobs->setUserId(USER_JOHN);
        $this->jobs->setProjectId(PROJECT_WEBSITE);
    }

    public function testFetchAllByUserAndProject()
    {
        $jobs = $this->jobs->fetchAll();

        $this->assertEquals(JOB_WEBSITE_TOTAL, count($jobs));

        return $jobs;
    }

    public function testCreateJob()
    {
        $this->jobs->add(TASK_FRONT_END, 19.5, 20);
        $this->assertEquals(JOB_WEBSITE_TOTAL + 1, count($this->jobs->fetchAll()));
    }

    public function testAppendingTimeToJob()
    {
        $this->jobs->add(TASK_FRONT_END, 17, 17.5);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 17.5);
        $this->assertEquals(JOB_WEBSITE_TOTAL, count($this->jobs->fetchAll()));
    }

    public function testPrependingTimeToJob()
    {
        $this->jobs->add(TASK_FRONT_END, 12, 13);

        // find job affect
        $job = $this->jobs->findByStartTime(12);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(JOB_WEBSITE_TOTAL, count($this->jobs->fetchAll()));
    }

    public function testPrependOverlappingTimeToJob()
    {
        $this->jobs->add(TASK_FRONT_END, 12, 13.25);

        // find job affect
        $job = $this->jobs->findByStartTime(12);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(JOB_WEBSITE_TOTAL, count($this->jobs->fetchAll()));
    }

    public function testCombineJobs()
    {
        $this->jobs->add(TASK_FRONT_END, 17, 18);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 18.50);
        $this->assertEquals(JOB_WEBSITE_TOTAL - 1, count($this->jobs->fetchAll()));
    }

    public function testEngulfJobs()
    {
        $this->jobs->add(TASK_FRONT_END, 12, 20);

        // find job affect
        $job = $this->jobs->findByStartTime(12);

        $this->assertEquals($job->stop_time, 20);
        $this->assertEquals(1, count($this->_fetchFromAllProjects()));
    }

    public function testAppendingOtherTask()
    {
        $this->jobs->add(TASK_BACK_END, 17, 17.5);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 17.0);
        $this->assertEquals(JOB_WEBSITE_TOTAL + 1, count($this->jobs->fetchAll()));
    }

    public function testPrependingOtherTask()
    {
        $this->jobs->add(TASK_BACK_END, 12, 13);

        // find job affect
        $job = $this->jobs->findByStartTime(13);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(JOB_WEBSITE_TOTAL + 1, count($this->jobs->fetchAll()));
    }

    public function testPrependingOtherTaskOverlapping()
    {
        $this->jobs->add(TASK_BACK_END, 12, 13.25);

        // find job affect
        $job = $this->jobs->findByStartTime(13.25);

        $this->assertEquals($job->stop_time, 16.25);
        $this->assertEquals(JOB_WEBSITE_TOTAL + 1, count($this->jobs->fetchAll()));
    }

    public function testAppendingOtherTaskOverlapping()
    {
        $this->jobs->add(TASK_BACK_END, 16.75, 17.5);

        // find job affect
        $job = $this->jobs->findByStartTime(16.5);

        $this->assertEquals($job->stop_time, 16.75);
        $this->assertEquals(JOB_WEBSITE_TOTAL + 1, count($this->jobs->fetchAll()));
    }

    public function testSeparateExistingJob()
    {
        $this->jobs->add(TASK_BACK_END, 15.25, 15.75);

        $oldJobPart1 = $this->jobs->findByStartTime(13);
        $this->assertEquals($oldJobPart1->stop_time, 15.25);
        $this->assertEquals($oldJobPart1->task_id, TASK_FRONT_END);

        $oldJobPart2 = $this->jobs->findByStartTime(15.75);
        $this->assertEquals($oldJobPart2->stop_time, 16.25);
        $this->assertEquals($oldJobPart2->task_id, TASK_FRONT_END);

        $newJob = $this->jobs->findByStartTime(15.25);
        $this->assertEquals($newJob->stop_time, 15.75);
        $this->assertEquals(JOB_WEBSITE_TOTAL + 2, count($this->jobs->fetchAll()));
    }

    public function testOtherTaskOverwritingJob()
    {
        $this->jobs->add(TASK_BACK_END, 18, 19);

        // find job affect
        $job = $this->jobs->findByStartTime(18);

        $this->assertEquals($job->stop_time, 19);
        $this->assertEquals($job->task_id, TASK_BACK_END);
        $this->assertEquals(JOB_WEBSITE_TOTAL, count($this->jobs->fetchAll()));
    }

    public function testOtherOverwritesMultipleJobs()
    {
        $this->jobs->add(TASK_BACK_END, 14.5, 18.75);

        $oldJob1 = $this->jobs->findByStartTime(13);
        $this->assertEquals($oldJob1->stop_time, 14.5);
        $this->assertEquals($oldJob1->task_id, TASK_FRONT_END);

        $job = $this->jobs->findByStartTime(14.5);
        $this->assertEquals($job->stop_time, 18.75);
        $this->assertEquals($job->task_id, TASK_BACK_END);

        $this->assertEquals(JOB_WEBSITE_TOTAL - 2, count($this->jobs->fetchAll()));

        $this->jobs->setProjectId(PROJECT_ECOMMERCE);
        $oldJob2 = $this->jobs->findByStartTime(18.75);
        $this->assertEquals($oldJob2->stop_time, 19);
        $this->assertEquals($oldJob2->task_id, TASK_FRONT_END);

        $this->assertEquals(JOB_PROJECTS_TOTAL - 2, count($this->_fetchFromAllProjects()));
    }

    public function testOtherJobOverwriteAndCombine()
    {
        $this->jobs->add(TASK_BACK_END, 16.50, 18.75);

        $oldJob1 = $this->jobs->findByStartTime(13);
        $this->assertEquals($oldJob1->stop_time, 16.25);
        $this->assertEquals($oldJob1->task_id, TASK_FRONT_END);

        $job = $this->jobs->findByStartTime(16.25);
        $this->assertEquals($job->stop_time, 18.75);
        $this->assertEquals($job->task_id, TASK_BACK_END);

        $this->assertEquals(JOB_WEBSITE_TOTAL - 2, count($this->jobs->fetchAll()));

        $this->jobs->setProjectId(PROJECT_ECOMMERCE);
        $oldJob2 = $this->jobs->findByStartTime(18.75);
        $this->assertEquals($oldJob2->stop_time, 19);
        $this->assertEquals($oldJob2->task_id, TASK_FRONT_END);
    }

    public function testDeleteMultipleJobs()
    {
        $this->jobs->remove(13, 17.25);

        // get remaining jobs
        $jobs = $this->jobs->fetchAll();

        $this->assertEquals(JOB_WEBSITE_TOTAL - 3, count($jobs));
        $this->assertEquals($jobs->current()->start_time, 18.00);
    }

    public function testDeleteBeginningOfJob()
    {
        $this->jobs->remove(12.75, 13.5);

        // get remaining jobs
        $job = $this->jobs->findByStartTime(13.5);

        $this->assertEquals(JOB_WEBSITE_TOTAL, count($this->jobs->fetchAll()));
        $this->assertEquals($job->stop_time, 16.25);
    }

    public function testDeleteEndOfJob()
    {
        $this->jobs->remove(15.75, 16.25);

        // get remaining jobs
        $job = $this->jobs->findByStartTime(13);

        $this->assertEquals(JOB_WEBSITE_TOTAL, count($this->jobs->fetchAll()));
        $this->assertEquals($job->stop_time, 15.75);
    }

    public function testDeleteAndSplitJob()
    {
        $this->jobs->remove(14, 15);

        $jobPart1 = $this->jobs->findByStartTime(13);
        $jobPart2 = $this->jobs->findByStartTime(15);

        $this->assertEquals(JOB_WEBSITE_TOTAL + 1, count($this->jobs->fetchAll()));
        $this->assertEquals($jobPart1->stop_time, 14);
        $this->assertEquals($jobPart2->stop_time, 16.25);
    }

    public function testDeleteProject1()
    {
        $this->jobs->remove(13, 19);

        $this->assertEquals(0, count($this->jobs->fetchAll()));

        // make sure project 2 is not affected
        $this->jobs->setProjectId(PROJECT_ECOMMERCE);
        $this->assertEquals(1, count($this->jobs->fetchAll()));
    }

    public function testAddAndGetJobsFromProject1()
    {
        $projects  = App_Model_Projects::getInstance();
        $website   = $projects->find(PROJECT_WEBSITE);
        $eCommerce = $projects->find(PROJECT_ECOMMERCE);
        $projects  = array(
            PROJECT_WEBSITE   => $website,
            PROJECT_ECOMMERCE => $eCommerce,
        );

        // assign jobs to each project
        foreach ($this->_fetchFromAllProjects() as $job) {
            $projects[$job->project_id]->addJob($job);
        }

        $this->assertEquals(4, count($website->getJobs()));
        $this->assertEquals(1, count($eCommerce->getJobs()));

        // test conversations
        $this->assertInstanceof('App_Model_Job', $website->getJob(13));
        $this->assertInstanceof('App_Model_Job', $website->getJob(13.0));
        $this->assertInstanceof('App_Model_Job', $website->getJob('13.0'));
        $this->assertInstanceof('App_Model_Job', $website->getJob(16.5));
        $this->assertInstanceof('App_Model_Job', $website->getJob('16.50'));
        $this->assertFalse($website->getJob('1.00'));
    }

    private function _fetchFromAllProjects()
    {
        // search through entire all project jobs
        $this->jobs->setProjectId(null);

        return $this->jobs->fetchAll();
    }
}