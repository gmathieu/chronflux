<?php

class App_User_Controller_Settings extends App_User_Controller_Action
{
    public function init()
    {
        parent::init();

        $this->view->headLink()->appendStylesheet($this->view->baseUrl('css/settings.css'));
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->isAjax()) {
            $this->getHelper('viewRenderer')->setNoRender();
        }
    }

    public function postDispatch()
    {
        parent::postDispatch();

        $this->view->content = $this->view->render("{$this->controllerName}/{$this->actionName}.phtml");

        $this->getHelper('viewRenderer')->renderScript('settings/_layout.phtml');
    }
}