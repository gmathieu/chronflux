<?php
class App_Model_Project extends App_Model_Abstractable
{
    protected $_jobs;

    public function init()
    {
        parent::init();

        $this->_jobs = array();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->title;
    }

    public function addJob(App_Model_Job $job)
    {
        $this->_jobs[$job->start_time] = $job;
    }

    public function getJob($time)
    {
        $time = number_format($time, 2);
        if (isset($this->_jobs[$time])) {
            return $this->_jobs[$time];
        } else {
            return false;
        }
    }

    public function getJobs()
    {
        return $this->_jobs;
    }

    public function canDelete()
    {
        return $this->total_users == 0;
    }

    public function delete()
    {
        return App_Model_Projects::getInstance()->delete($this);
    }

    public function update(array $data)
    {
        $this->setFromArray($data);
        return App_Model_Projects::getInstance()->update($this);
    }

    public static function create(array $data)
    {
        $project = new self($data);

        App_Model_Projects::getInstance()->insert($project);

        return $project;
    }
}