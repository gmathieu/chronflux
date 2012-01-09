<?php

class User_JobsController extends App_User_Controller_Action
{
    public function init()
    {
        parent::init();

        // ajax contexts
        $this->_helper->getHelper('AjaxContext')
            ->addActionContext('add', 'json')
            ->addActionContext('remove', 'json')
            ->initContext();

        // convert username to user ID
        $this->_setParam('user_id', $this->user->id);
    }

    public function addAction()
    {
        $jobs = App_Model_Jobs::getInstance();
        $form = new User_Form_Job();

        // use form validation to check data
        $this->_validateFormData($form);

        $data = $form->getValues();

        // assign job details
        $jobs->setDate($data['date']);
        $jobs->setUserId($data['user_id']);
        $jobs->setProjectId($data['project_id']);

        // add job
        $jobs->add($data['task_id'], $data['start_time'], $data['stop_time']);

        $this->_addRemoveRedirect($form);
    }

    public function removeAction()
    {
        $jobs = App_Model_Jobs::getInstance();
        $form = new User_Form_Job();

        // remove fields not required
        $form->task_id->setRequired(false);

        // use form validation to check data
        $this->_validateFormData($form);

        $data = $form->getValues();

        // assign job details
        $jobs->setUserId($data['user_id']);
        $jobs->setProjectId($data['project_id']);

        // remove jobs
        $jobs->remove($data['start_time'], $data['stop_time']);

        $this->_addRemoveRedirect($form);
    }

    private function _validateFormData($form)
    {
        if (!$form->isValid($this->getRequest()->getParams())) {
            throw new Exception(print_r($form->getMessages(null), true));
        }
    }

    private function _addRemoveRedirect($form)
    {
        if (!$this->isAjax()) {
            return $this->_redirector->gotoUrl("/user/{$this->user->username}/timesheets/manage/date/{$form->date->getValue()}");
        }
    }
}