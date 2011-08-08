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
}

