<?php
class App_Model_Jobs extends Mg_Data_Service
{
    private $_date;
    private $_projectId;
    private $_userId;

    private $_ignoreProjectId = false;

    public function init()
    {
        parent::init();

        $this->select->order('start_time ASC');
        $this->select->order('created_on DESC');
    }

    public function setDate($date)
    {
        $this->_date = $date;
    }

    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    public function setProjectId($projectId)
    {
        $this->_projectId = $projectId;
    }

    public function add($taskId, $startTime, $stopTime)
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

        // resolve conflicts
        $this->_resolveAddTimeConflicts($taskId, $startTime, $stopTime);
    }

    public function remove($startTime, $stopTime)
    {
        $list = $this->_findAffected($startTime, $stopTime);

        foreach ($list as $job) {
            // new times surround job
            if ($startTime <= $job->start_time  && $job->stop_time <= $stopTime) {
                $this->delete($job);
                continue;

            // new times overlap job's start time
            } else if ($startTime <= $job->start_time && $job->start_time <= $stopTime) {
                $job->start_time = $stopTime;

            // new times overlap job's stop time
            } else if ($startTime <= $job->stop_time && $job->stop_time <= $stopTime) {
                $job->stop_time = $startTime;

            // new times inside job
            } else if ($job->start_time < $startTime && $stopTime < $job->stop_time) {
                $this->_cloneWithNewStartTime($job, $stopTime);
                $job->stop_time = $startTime;
            }

            // save changes
            $this->update($job);
        }
    }

    public function fetchAll()
    {
        // filter date
        if ($this->_date) {
            $this->select->where('date = ?', $this->_date);
        }

        // filter user ID
        if ($this->_userId) {
            $this->select->where('user_id = ?', $this->_userId);
        }

        // filter project ID unless ignored
        if (false === $this->_ignoreProjectId && $this->_projectId) {
            $this->select->where('project_id = ?', $this->_projectId);
        }

        return parent::fetchAll();
    }

    private function _resolveAddTimeConflicts($taskId, $startTime, $stopTime)
    {
        $list = $this->_findAffected($startTime, $stopTime, true);

        // a minimum of 2 jobs is required for a conflict
        if (count($list) > 1) {
            $olderJob = null;
            foreach ($list as $newerJob) {
                // store a reference of the previous job
                if (is_null($olderJob)) {
                    $olderJob = $newerJob;
                } else {
                    // same task ID: merge
                    if ($newerJob->task_id == $olderJob->task_id) {
                        $jobToProcess = $this->_merge($olderJob, $newerJob);
                    } else {
                        // new task ID: break apart
                        $jobToProcess = $this->_break($olderJob, $newerJob, $taskId);

                        // delete jobs with negative time
                        if ($jobToProcess->start_time >= $jobToProcess->stop_time) {
                            $this->delete($jobToProcess);
                        } else {
                            $this->update($jobToProcess);

                            // update older job
                            $olderJob = $newerJob;
                        }
                    }
                }
            }
        }
    }

    private function _findAffected($startTime, $stopTime, $ignoreProjectId = false)
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

        // overide find affected across all projects
        $this->_ignoreProjectId = $ignoreProjectId;

        $rowSet = $this->fetchAll();

        // restore project ID
        $this->_ignoreProjectId = false;

        return $rowSet;
    }

    private function _merge(App_Model_Job $olderJob, App_Model_Job $newerJob)
    {
        // check to see that older job doesn't engulf newer job
        if ($olderJob->stop_time < $newerJob->stop_time) {
            // append newer job's stop time to older job
            $olderJob->stop_time = $newerJob->stop_time;
            $this->update($olderJob);
        }

        // delete newer job
        $this->delete($newerJob);
    }

    private function _break(App_Model_Job $olderJob, App_Model_Job $newerJob, $newJobTaskId)
    {
        // older job has new task ID therefor has priority
        if ($olderJob->task_id == $newJobTaskId) {
            // update start time
            $newerJob->start_time = $olderJob->stop_time;
            $jobToProcess         = $newerJob;
        } else {
            // if old job's stop time goes past new job's stop time, split old job in two
            if ($olderJob->stop_time > $newerJob->stop_time) {
                $this->_cloneWithNewStartTime($olderJob, $newerJob->stop_time);
            }
    
            // update old job's stop time to new job's start time
            $olderJob->stop_time = $newerJob->start_time;

            // return old job for processing
            $jobToProcess = $olderJob;
        }

        return $jobToProcess;
    }

    private function _cloneWithNewStartTime(App_Model_Job $clonable, $startTime)
    {
        $job             = new App_Model_Job($clonable->getRawData());
        $job->id         = null;
        $job->start_time = $startTime;
        $this->insert($job);
    }
}