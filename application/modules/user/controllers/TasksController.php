<?php

class User_TasksController extends App_User_Controller_Settings
{
    public function newAction()
    {
        $nextUrl = $this->_getParam('next_url');
        $request = $this->getRequest();

        if ($nextUrl) {
            $this->_afterSave = function($form, $userDataObj) use ($nextUrl, $request) {
                // append task_id to next URL
                $request->setParam('next_url', $nextUrl . '/task_id/' . $userDataObj->task_id);
            };
        }

        parent::newAction();
    }

    public function editAction()
    {
        $this->_beforeSave = function($form, $userDataObj) {
            $userDataObj->setColor($form->color->getValue());
        };

        parent::editAction();
    }

    protected function _initForm()
    {
        $form = parent::_initForm();

        if ('new' === $this->actionName) {
            $form->color->addValidator(new User_Form_Validate_UniqueColor());
        }

        // add color picker plugin
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('plugins/colorpicker/css/colorpicker.css'));
        $this->view->inlineScript()->appendFile($this->view->baseUrl('plugins/colorpicker/js/colorpicker.js'));

        return $form;
    }
}