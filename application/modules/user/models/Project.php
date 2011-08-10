<?php
class User_Model_Project extends App_Model_Project
{
    const ACTIVE   = 1;
    const INACTIVE = 0;

    public function fetchJobs($date)
    {
        $jobs = App_Model_Jobs::getInstance();
        $jobs->setUserId($this->user_id);
        $jobs->setProjectId($this->project_id);
        $jobs->setDate($date);

        return $jobs->fetchAll();
    }

    public function activate()
    {
        $this->active = self::ACTIVE;
        return User_Model_Projects::getInstance()->update($this);
    }

    public function deactivate()
    {
        $this->active = self::INACTIVE;
        return User_Model_Projects::getInstance()->update($this);
    }

    public static function create($data)
    {
        // create new project
        $project = parent::create($data);

        // assign user to project
        $userProjectData = array_merge($data, $project->getRawData());
        $userProjectData['project_id'] = $project->id;
        $userProject = new self($userProjectData);

        // save user project data
        User_Model_Projects::getInstance()->insert($userProject);

        return $userProject;
    }
}