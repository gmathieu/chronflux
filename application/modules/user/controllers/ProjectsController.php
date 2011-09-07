<?php

class User_ProjectsController extends App_User_Controller_Settings
{
    public $userProjects;
    public $userProjectSet;

    public function init()
    {
        parent::init();

        // context switch
        $this->_helper->getHelper('AjaxContext')
                      ->addActionContext('list', 'html')
                      ->initContext();

        // init projects service
        $this->userProjects = User_Model_Projects::getInstance();
        $this->userProjects->setUserId($this->user->id);

        // get a list of projects
        $this->userProjectSet = $this->userProjects->fetchAll();
    }

    public function preDispatch()
    {
        parent::preDispatch();

        // always show list of user projects
        $this->view->userProjectSet = $this->userProjectSet;
    }

    public function listAction()
    {
    }

    public function editAction()
    {
        $project = $this->_requireProject();

        $this->view->project = $project;
    }

    private function _requireProject()
    {
        $projectId = $this->_getParam('project_id');
        $project   = $this->userProjects->findByProjectId($projectId);

        if ($project) {
            return $project;
        } else {
            throw new Exception("Project {$projectId} not found.");
        }
    }
}