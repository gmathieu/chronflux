<?php

class IndexController extends App_Controller_Action
{
    public function indexAction()
    {
        // check that a user is logged in and redirect home
        if (isset($this->session->user)) {
            $this->_redirector->gotoUrl("/user/{$this->session->user->username}/timesheets/manage");
        } else if (isset($_COOKIE['user_service_type'])) {
            $this->_forward('auto', 'auth');
        } else {
            $this->_forward('index', 'auth');
        }
    }
}