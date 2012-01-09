<?php

class User_JobsController extends App_User_Controller_Action
{
    public function init()
    {
        parent::init();

        $this->_helper->getHelper('AjaxContext')
            ->addActionContext('add', 'json')
            ->initContext();
    }

    public function addAction()
    {
        $jobs = App_Model_Jobs::getInstance();
        $form = new User_Form_Job();

        // set user ID to params
        $this->_setParam('user_id', $this->user->id);

        // check form validation
        if (!$form->isValid($this->getRequest()->getParams())) {
            throw new Exception(print_r($form->getMessages(null), true));
        }

        $data = $form->getValues();

        // assign job details
        $jobs->setDate($data['date']);
        $jobs->setUserId($data['user_id']);
        $jobs->setProjectId($data['project_id']);

        // add job hours
        $jobs->add($data['task_id'], $data['start_time'], $data['stop_time']);

        if (!$this->isAjax()) {
            return $this->_redirector->gotoUrl("/user/{$this->user->username}/timesheets/manage/date/{$date}");
        }
    }

    public function removeAction()
    {
    }
}