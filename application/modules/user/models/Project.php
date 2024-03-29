<?php
class User_Model_Project extends App_Model_Project
{
    const ACTIVE   = 1;
    const INACTIVE = 0;

    public function getId()
    {
        return $this->project_id;
    }

    public function canDelete()
    {
        return $this->total_hours == 0;
    }

    public function fetchJobs($date)
    {
        $jobs = App_Model_Jobs::getInstance();
        $jobs->setUserId($this->user_id);
        $jobs->setProjectId($this->project_id);
        $jobs->setDate($date);

        return $jobs->fetchAll();
    }

    public function isActive()
    {
        return self::ACTIVE == $this->active;
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

    public function update(array $data)
    {
        // update user project
        $this->setFromArray($data);
        $updated = User_Model_Projects::getInstance()->update($this);

        // get and update original projet
        $project = App_Model_Projects::getInstance()->find($this->project_id);
        return $project->update($data) && $updated;
    }

    public function delete()
    {
        $userProjects = User_Model_Projects::getInstance();
        $userProjects->delete($this);

        // get parent project
        $projects = App_Model_Projects::getInstance();
        $project  = $projects->find($this->project_id);

        // delete parent project if un-used
        if ($project->canDelete()) {
            $project->delete();
        }
    }

    public static function create(array $data)
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