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

    protected function _initRoutes()
    {
		$this->bootstrap('frontController');
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'production');
		$router = $this->frontController->getRouter();
		$router->addConfig($config, 'routes');
    }

    protected function _initCustomView()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        // CSS
        $view->headLink()->appendStylesheet($view->baseUrl('css/yui-reset-fonts-3.3.0.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('fonts/play-fontfacekit/stylesheet.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('css/global.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('css/buttons.css'));

        $view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'View_Helper');

        return $view;
    }
}

