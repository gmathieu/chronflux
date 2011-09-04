<?php

class User_ProjectsController extends App_User_Controller_Action
{
    public function init()
    {
        parent::init();

        // context switch
        $this->_helper->getHelper('AjaxContext')
                      ->addActionContext('list', 'html')
                      ->initContext();

        // init projects service
        $this->projects = User_Model_Projects::getInstance();
        $this->projects->setUserId($this->user->id);
    }

    public function listAction()
    {
        // get a list of projects
        $projectSet = $this->projects->fetchAll();

        // assign to view variables
        $this->view->projectSet = $projectSet;
    }
}