<?php
class App_Model_Jobs extends Mg_Data_Service
{
    private $_date;
    private $_projectId;
    private $_userId;

    public function init()
    {
        parent::init();

        $this->select->order('start_time ASC');
        $this->select->order('created_on DESC');
    }

    public function setDate($date)
    {
        $this->_date = $date;
        $this->select->where('date = ?', $date);
    }

    public function setUserId($userId)
    {
        $this->_userId = $userId;
        $this->select->where('user_id = ?', $userId);
    }

    public function setProjectId($projectId)
    {
        $this->_projectId = $projectId;
        $this->select->where('project_id = ?', $projectId);
    }

    public function addTime($taskId, $startTime, $stopTime)
    {
        // insert new time
        $this->insert(new App_Model_Job(array(
            'user_id'    => $this->_userId,
            'project_id' => $this->_projectId,
            'date'       => $this->_date,
            'task_id'    => $taskId,
            'start_time' => $startTime,
            'stop_time'  => $stopTime
        )));

        // get list of affected jobs to resolve conflicts
        $list = $this->_findAffected($taskId, $startTime, $stopTime);

        // a minimum of 2 jobs is required for a conflict
        if (count($list) > 1) {
            $prevJob = null;
            foreach ($list as $job) {
                // store a reference of the previous job
                if (is_null($prevJob)) {
                    $prevJob = $job;
                } else {
                    // same task ID: merge
                    if ($job->task_id == $prevJob->task_id) {

                        // append to previous job
                        if ($prevJob->stop_time < $job->stop_time) {
                            $prevJob->stop_time = $job->stop_time;
                            $this->update($prevJob);
                        }
                        // delete current job
                        $this->delete($job);

                    // new task ID: break apart
                    } else {
                        // previous job has new task ID
                        if ($prevJob->task_id == $taskId) {
                            // update start time
                            $job->start_time = $prevJob->stop_time;

                            $jobToProcess = $job;
                        } else {
                            // new task splits old job in two
                            if ($prevJob->stop_time > $job->stop_time) {
                                // create new job
                                $newJob             = new App_Model_Job($prevJob->getRawData());
                                $newJob->id         = null;
                                $newJob->start_time = $job->stop_time;
                                $this->insert($newJob);
                            }
                            $prevJob->stop_time = $job->start_time;

                            $jobToProcess = $prevJob;
                        }

                        // delete jobs with negative time
                        if ($jobToProcess->start_time >= $jobToProcess->stop_time) {
                            $this->delete($jobToProcess);
                        } else {
                            $this->update($jobToProcess);

                            // update previous job
                            $prevJob = $job;
                        }
                    }
                }
            }
        }
    }

    protected function _findAffected($taskId, $startTime, $stopTime)
    {
        $startOverlap = array();
        $stopOverlap  = array();
        $engulf       = array();

        // find jobs with overlapping times
        $startOverlap[] = $this->adapter->quoteInto('start_time < ?', $startTime, 'DECIMAL');
        $startOverlap[] = $this->adapter->quoteInto('stop_time >= ?', $startTime, 'DECIMAL');
        $stopOverlap[]  = $this->adapter->quoteInto('start_time <= ?', $stopTime, 'DECIMAL');
        $stopOverlap[]  = $this->adapter->quoteInto('stop_time > ?', $stopTime, 'DECIMAL');
        $engulf[]       = $this->adapter->quoteInto('start_time >= ?', $startTime, 'DECIMAL');
        $engulf[]       = $this->adapter->quoteInto('stop_time <= ?', $stopTime, 'DECIMAL');

        // combine statements
        $startOverlap = implode(' AND ', $startOverlap);
        $stopOverlap  = implode(' AND ', $stopOverlap);
        $engulf       = implode(' AND ', $engulf);
        $this->select->where("({$startOverlap}) OR ({$stopOverlap}) OR ({$engulf})");

        return $this->fetchAll();
    }
}