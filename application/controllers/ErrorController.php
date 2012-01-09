<?php

class ErrorController extends Zend_Controller_Action
{

    public function init()
    {
        parent::init();

        // auto formatter
        $this->_initAutoFormatter();

        // disable layout
        $this->_helper->layout->disableLayout();
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exceptionMessage = $errors->exception->getMessage();
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }

    private function _initAutoFormatter()
    {
        $format  = $this->_getParam('format');

        // check to see if format isn't set and call is ajax
        if (!$format && $this->_request->isXmlHttpRequest()) {
            $acceptHeader = $this->_request->getHeader('Accept');

            // check the request header to accept json (jQuery only?)
            if (false === stristr($acceptHeader, 'application/json')) {
                $this->_request->setParam('format', 'html');
            } else {
                $this->_request->setParam('format', 'json');
            }
        }

        // init view formatter
		$this->_helper->getHelper('AjaxContext')
                      ->addActionContext('error', 'json')
                      ->addActionContext('error', 'html')
                      ->initContext();
    }
}

