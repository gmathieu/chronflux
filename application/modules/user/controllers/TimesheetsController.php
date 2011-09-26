<?php

class User_TimesheetsController extends App_User_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        // setup view variables
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('css/timesheets.css'));
        $this->view->selectedMenuItem = 'timesheets';
    }

    public function manageAction()
    {
        $date         = $this->_getParam('date');
        $userProjects = User_Model_Projects::getInstance();
        $userTasks    = User_Model_Tasks::getInstance();

        // make sure date is valid
        if (!$date) {
            return $this->_forward('current-date');
        } else {
            try {
                $dateObj = new Zend_Date($date, Zend_Date::ISO_8601);
            } catch(Exception $e) {
                return $this->_forward('current-date');
            }
        }

        // set proper user
        $userTasks->setUserId($this->user->id);
        $userProjects->setUserId($this->user->id);

        // get all user projects
        $userProjectSet = $userProjects->fetchByDateOrActive($date);

        // get all user tasks
        $userTaskSet = $userTasks->fetchAll();

        // assign to view
        $this->view->date           = $date;
        $this->view->dateObj        = $dateObj;
        $this->view->userProjectSet = $userProjectSet;
        $this->view->userTaskSet    = $userTaskSet;
    }

    public function currentDateAction()
    {
    }
}