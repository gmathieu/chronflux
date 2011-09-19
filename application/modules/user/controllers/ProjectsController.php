<?php

class User_ProjectsController extends App_User_Controller_Settings
{
    public function activateAction()
    {
        $userProject = $this->_requireDataObj();
        $userProject->activate();

        return $this->_redirectToEditAction($userProject);
    }

    public function deactivateAction()
    {
        $userProject = $this->_requireDataObj();
        $userProject->deactivate();

        return $this->_redirectToEditAction($userProject);
    }
}