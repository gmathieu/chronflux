<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initConfig()
	{
		$config = new Zend_Config($this->getOptions());
		Zend_Registry::set('config', $config);

		return $config;
	}

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
        $view->headLink()->appendStylesheet($view->baseUrl('css/lists.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('css/layouts.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('css/forms.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('css/bubbles.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('css/tooltip.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('plugins/jquery-ui-1.8.17.custom/css/ui-darkness/jquery-ui-1.8.17.custom.css'));

        // setup JS namespace
        $headScript = $this->_renderHeaderScript($view);
        $view->headScript()->setScript($headScript);

        // include JS dev tools
        if ('development' == APPLICATION_ENV) {
            $view->inlineScript()->appendFile($view->baseUrl('javascript/dev-tools.js'));
        }

        $view->inlineScript()->appendFile($view->baseUrl('javascript/jquery-1.7.1.min.js'));
        $view->inlineScript()->appendFile($view->baseUrl('plugins/jquery-ui-1.8.17.custom/js/jquery-ui-1.8.17.custom.min.js'));
        $view->inlineScript()->appendFile($view->baseUrl('javascript/utils.js'));
        $view->inlineScript()->appendFile($view->baseUrl('javascript/tooltip.js'));
        $view->inlineScript()->appendFile($view->baseUrl('javascript/buttons.js'));
        $view->inlineScript()->appendFile($view->baseUrl('javascript/bubbles.js'));
        $view->inlineScript()->appendFile($view->baseUrl('javascript/timesheets.js'));
        $view->inlineScript()->appendFile($view->baseUrl('javascript/timesheets/clock.js'));
        $view->inlineScript()->appendFile($view->baseUrl('javascript/timesheets/tasks.js'));
        $view->inlineScript()->appendFile($view->baseUrl('javascript/timesheets/projects.js'));
        $view->inlineScript()->appendFile($view->baseUrl('javascript/timesheets/jobs.js'));
        $view->inlineScript()->appendFile($view->baseUrl('javascript/settings.js'));

        $view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'View_Helper');

        return $view;
    }

    private function _renderHeaderScript($view)
    {
        $baseUrl = Zend_Json::encode($view->baseUrl());
        return <<<JS
        var Chronflux = Chronflux || {};
        Chronflux.BASE_URL = {$baseUrl};
JS;
    }
}