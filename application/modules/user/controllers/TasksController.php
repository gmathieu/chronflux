<?php

class User_TasksController extends App_User_Controller_Settings
{
    protected function _initForm()
    {
        $form = parent::_initForm();

        if ('new' === $this->actionName) {
            $form->color->addValidator(new User_Form_Validate_UniqueColor());
        }

        return $form;
    }
}