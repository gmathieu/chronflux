<?php

class App_Controller_Action extends Zend_Controller_Action
{
    public $controllerName;
    public $actionName;
    public $session;

    protected $_redirector;

    public function init()
    {
        parent::init();

        $this->controllerName = $this->getRequest()->getControllerName();
        $this->actionName     = $this->getRequest()->getActionName();
        $this->session        = new Zend_Session_Namespace('chronflux');
        $this->_redirector    = $this->_helper->getHelper('Redirector');

        // ajax auto formatter
        $this->_setAjaxAutoFormat();
    }

    public function preDispatch()
    {
        $this->view->controllerName = $this->controllerName;
        $this->view->actionName     = $this->actionName;
        $this->view->session        = $this->session;

        // show grid
        if ($this->_getParam('show-grid')) {
            $url = $this->view->baseUrl('images/24_col.gif');
            $this->view->headStyle()->appendStyle("html { background: #ffffff url({$url}) -5px 0 repeat; }");
        }
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

    private function _setAjaxAutoFormat()
    {
        // check to see if format isn't set and call is ajax
        if ($this->isAjax() && !$this->_getParam('format')) {
            $JSONHeader = $this->getRequest()->getHeader('Accept');

            // check the request header to accept json (jQuery only?)
            if (false === stristr($JSONHeader, 'application/json')) {
                $this->getRequest()->setParam('format', 'html');
            } else {
                $this->getRequest()->setParam('format', 'json');
            }
        }
    }
}