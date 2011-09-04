<?php
class App_User_Controller_Action extends App_Controller_Action
{
    public $user;

    public function init()
    {
        parent::init();

        $this->user = $this->_requireUser();
    }

    public function postDispatch()
    {
        $this->view->user = $this->user;
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