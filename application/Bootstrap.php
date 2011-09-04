<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initStoreDbAdpater()
    {
        $resource = $this->getPluginResource('db');
        $db       = $resource->getDbAdapter();

        // save db adapter to registry
        Zend_Registry::set('db', $db);

        return $db;
    }

    protected function _initCustomView()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'View_Helper');

        return $view;
    }
}

