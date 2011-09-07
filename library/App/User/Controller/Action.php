<?php
class App_User_Controller_Action extends App_Controller_Action
{
    public $user;

    public function init()
    {
        parent::init();

        $this->user = $this->_requireUser();
    }

    public function preDispatch()
    {
        parent::preDispatch();

        // use user layout
        $this->getHelper('layout')->setLayout('users');

        // assign current username to view
        $this->view->username = $this->user->username;
    }

    protected function _requireUser()
    {
        $user     = false;
        $username = $this->_getParam('username');

        if ($username) {
            $users = App_Model_Users::getInstance();
            $user  = $users->findByUsername($username);
        }

        if ($user) {
            return $user;
        } else {
            throw new Exception('A user is required');
        }        
    }
}