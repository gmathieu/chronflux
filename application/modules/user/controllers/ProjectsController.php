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

    public function newAction()
    {
        $form = $this->_initForm();

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            $userProject = User_Model_Project::create($this->_request->getPost());
            return $this->_redirectToEditAction($userProject);
        }
    }

    public function editAction()
    {
        $userProject = $this->_requireProject();
        $form        = $this->_initForm();

        // pre-populate form with user project's data
        $form->populate($userProject->getRawData());

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            // update user project with POST data
            $userProject->setFromArray($this->_request->getPost());
            $this->userProjects->update($userProject);
        }

        $this->view->userProject = $userProject;
    }

    public function activateAction()
    {
        $userProject = $this->_requireProject();
        $userProject->activate();

        return $this->_redirectToEditAction($userProject);
    }

    public function deactivateAction()
    {
        $userProject = $this->_requireProject();
        $userProject->deactivate();

        return $this->_redirectToEditAction($userProject);
    }

    private function _requireProject()
    {
        $projectId   = $this->_getParam('project_id');
        $userProject = $this->userProjects->findByProjectId($projectId);

        if ($userProject) {
            return $userProject;
        } else {
            throw new Exception("Project {$projectId} not found.");
        }
    }

    private function _initForm()
    {
        $form = new User_Form_Project();

        // force user_id
        $form->user_id->setValue($this->user->id);

        $this->view->form = $form;

        return $form;
    }

    private function _redirectToEditAction($userProject)
    {
        return $this->_redirect("user/{$this->user->username}/projects/edit/project_id/{$userProject->project_id}");
    }
}