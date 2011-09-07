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

        // disable automatic render to wrap content in settings layout
        if (!$this->isAjax()) {
            $this->getHelper('viewRenderer')->setNoRender();
        }

        $this->view->selectedMenuItem = 'settings';
    }

    public function postDispatch()
    {
        parent::postDispatch();

        $this->view->content = $this->view->render("{$this->controllerName}/{$this->actionName}.phtml");

        $this->getHelper('viewRenderer')->renderScript('settings/_layout.phtml');
    }
}