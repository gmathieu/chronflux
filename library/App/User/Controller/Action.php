<?php
class App_User_Controller_Action extends App_Controller_Action
{
    public $user;

    public function init()
    {
        parent::init();

        // check that user is logged in
        $this->_requireSessionUser();

        // get user
        $this->user = $this->_requireUser();

        // use user layout
        $this->getHelper('layout')->setLayout('users');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        // assign current user to view
        $this->view->user = $this->user;
    }

    protected function _requireSessionUser()
    {
        if (!isset($this->session->user)) {
            if ($this->isAjax()) {
                return $this->getResponse()->setHttpResponseCode(403);
            } else {
                $redirectUri = $this->getReturnPath($this->_request->getPathInfo());
                return $this->_redirector->gotoUrl($this->_request->getBaseUrl() . '/auth?redirect_uri=' . urlencode($redirectUri));
            }
        }
    }

    protected function _requireUser()
    {
        $user     = false;
        $username = $this->_getParam('username');

        if ($username) {
            // caching: return session user when username are the same
            if ($username == $this->session->user->username) {
                $user = $this->session->user;
            } else {
                // TODO: add support for viewing a different user profile
                throw new Exception('Permission to view this profile denied');
            }
        }

        if ($user) {
            return $user;
        } else {
            throw new Exception('A user is required');
        }
    }
}