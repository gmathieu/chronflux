<?php

class App_Controller_Action extends Zend_Controller_Action
{
    public $controllerName;
    public $actionName;

    public function init()
    {
        $this->controllerName = $this->getRequest()->getControllerName();
        $this->actionName     = $this->getRequest()->getActionName();

        if ($this->_getParam('show-grid')) {
            $url = $this->view->baseUrl('images/24_col.gif');
            $this->view->headStyle()->appendStyle("html { background: #ffffff url({$url}) -5px 0 repeat; }");
        }
    }

    public function preDispatch()
    {
        $this->view->controllerName = $this->controllerName;
        $this->view->actionName     = $this->actionName;
    }

    public function isAjax()
    {
        return $this->getRequest()->isXmlHttpRequest();
    }
}