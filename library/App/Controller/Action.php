<?php

class App_Controller_Action extends Zend_Controller_Action
{
    public function init()
    {
        if ($this->_getParam('show-grid')) {
            $url = $this->view->baseUrl('images/24_col.gif');
            $this->view->headStyle()->appendStyle("html { background: #ffffff url({$url}) -5px 0 repeat; }");
        }
    }
}