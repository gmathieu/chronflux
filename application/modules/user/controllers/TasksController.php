<?php

class User_TasksController extends App_User_Controller_Settings
{
    public $userTasks;
    public $userTaskSet;

    public function init()
    {
        parent::init();

        // init tasks service
        $this->userTasks = User_Model_Tasks::getInstance();
        $this->userTasks->setUserId($this->user->id);

        // get a list of tasks
        $this->userTaskSet = $this->userTasks->fetchAll();
    }

    public function preDispatch()
    {
        parent::preDispatch();

        // always show list of user tasks
        $this->view->userTaskSet = $this->userTaskSet;
    }

    public function listAction()
    {
    }
}