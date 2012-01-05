<?php

class App_Controller_Action extends Zend_Controller_Action
{
    public $controllerName;
    public $actionName;
    public $session;

    protected $_redirector;

    public function init()
    {
        $this->controllerName = $this->getRequest()->getControllerName();
        $this->actionName     = $this->getRequest()->getActionName();
        $this->session        = new Zend_Session_Namespace('chronflux');
        $this->_redirector    = $this->_helper->getHelper('Redirector');

        if ($this->_getParam('show-grid')) {
            $url = $this->view->baseUrl('images/24_col.gif');
            $this->view->headStyle()->appendStyle("html { background: #ffffff url({$url}) -5px 0 repeat; }");
        }
    }

    public function preDispatch()
    {
        $this->view->controllerName = $this->controllerName;
        $this->view->actionName     = $this->actionName;
        $this->view->session        = $this->session;
    }

    public function isAjax()
    {
        return $this->getRequest()->isXmlHttpRequest();
    }

    public function getReturnPath($path = '/')
    {
        $servername = $this->_request->getServer('SERVER_NAME');
        $baseUrl    = $this->getFrontController()->getBaseUrl();

        return "http://{$servername}{$baseUrl}{$path}";
    }
}