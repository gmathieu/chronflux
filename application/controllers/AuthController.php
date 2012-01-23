<?php

class AuthController extends App_Controller_Action
{
    private $_fb;
    private $_redirectUri;

    public function init()
    {
        parent::init();

        // redirect URI
        $this->_redirectUri = $this->_getParam('redirect_uri', $this->view->baseUrl('/'));

        // init facebook
        $fbConfig  = Zend_Registry::get('config')->fb->app->toArray();
        $this->_fb = new Facebook($fbConfig);
    }

    public function indexAction()
    {
        // assign URLs to view
        $this->view->fbLoginUrl = $this->_getFbLoginUrl();
    }

    public function autoAction()
    {
        $serviceType = false;
        $url         = false;

        if (isset($_COOKIE['user_service_type'])) {
            $serviceType = $_COOKIE['user_service_type'];
        }

        switch ($serviceType) {
            // Facebook oauth logic
            case App_Model_User::SERVICE_FB:
                $url = $this->_getFbLoginUrl();
            break;

            // Google oauth logic
            case App_Model_User::SERVICE_GOOGLE:
            break;

            // Twitter oauth logic
            case App_Model_User::SERVICE_TWITTER:
            break;
        }

        if ($url) {
            return $this->_redirector->gotoUrl($url);
        } else {
            return $this->_authError();
        }
    }

    public function newAction()
    {
        $username = $this->_getParam('username');
        $user     = false;

        if ('development' == APPLICATION_DEVELOPMENT && $username) {
            $users = App_Model_Users::getInstance();
            $user  = $users->findByUsername($username);
        }

        if ($user) {
            $this->_authSuccess($user);
        } else {
            $this->_authError();
        }
    }

    public function destroyAction()
    {
        if (isset($this->session->user)) {
            unset($this->session->user);
        }

        // expire cookie
        if (isset($_COOKIE['user_service_type'])) {
            setCookie('user_service_type', '', time() - 3600, '/', $_SERVER['SERVER_NAME']);
        }

        $this->_redirector->gotoUrl($this->view->baseUrl('/'));
    }

    public function registerAction()
    {
        // setup required data based on service
        $data = $this->_initRequiredData($this->_getParam('service'));

        // set default clock in time
        $data['clock_in_at'] = '9';

        // pre-popuplate form
        $form = new User_Form_User();
        $form->populate($data);

        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();

            // form validation
            if ($form->isValid($post)) {
                // merge service data with post value
                $data = array_merge($data, $form->getValues());

                // create user object
                $users = App_Model_Users::getInstance();
                $user  = new App_Model_User($data);
                $users->insert($user);

                // success callback
                $this->_authSuccess($user);
            }
        }

        // assign view variables
        $this->view->form = $form;
        $this->view->user = new App_Model_User($data);
    }

    public function fbAction()
    {
        $this->_redirectToRegistration($this->_fb->getUser(), App_Model_User::SERVICE_FB);
    }

    private function _authError()
    {
        // TODO: think about error fallbacks
        $encodedRedirectUri = urlencode($this->_redirectUri);
        return $this->_redirector->gotoUrl("/?redirect_uri={$encodedRedirectUri}");
    }

    private function _authSuccess(App_Model_User $user)
    {
        // store user in session
        $this->session->user = $user;

        // store service type in cookie for auto auth
        setCookie('user_service_type', $user->service_type, time() + 60*60*24*30, '/', $_SERVER['SERVER_NAME']);

        // redirect home
        return $this->_redirector->gotoUrl($this->_redirectUri);
    }

    private function _redirectToRegistration($serviceId, $serviceType)
    {
        // check that service ID and type are passed
        if (!$serviceId && !$serviceType) {
            return $this->_authError();
        }

        $users = App_Model_Users::getInstance();
        $user  = $users->findByServiceIdAndServiceType($serviceId, $serviceType);

        if (!$user) {
            // send user to registration
            $encodedRedirectUri = urlencode($this->_redirectUri);
            $this->_redirector->gotoUrl("/auth/register/service/{$serviceType}?redirect_uri={$encodedRedirectUri}");
        } else {
            // user already registered
            $this->_authSuccess($user);
        }
    }

    private function _initRequiredData($serviceType)
    {
        $data = array();

        switch ($serviceType) {
            // Facebook oauth logic
            case App_Model_User::SERVICE_FB:
                $data = $this->_initFbRequiredData();
            break;

            // Google oauth logic
            case App_Model_User::SERVICE_GOOGLE:
            break;

            // Twitter oauth logic
            case App_Model_User::SERVICE_TWITTER:
            break;
        }

        // validate required data
        if (empty($data['service_id']) && empty($data['service_type'])) {
            throw new Exception("service ID and service type need to be defined");
        }

        return $data;
    }

    private function _initFbRequiredData()
    {
        // get FB user ID
        $fbUserId = $this->_fb->getUser();

        if (!$fbUserId) {
            return $this->_authError();
        } else {
            // get FB user profile
            $fbUserProfile = $this->_fb->api('/me');

            // set form data
            $data = array(
                'service_id'   => $fbUserId,
                'service_type' => App_Model_User::SERVICE_FB,
                'username'     => ($fbUserId != $fbUserProfile['username']) ? $fbUserProfile['username'] : '',
                'name'         => $fbUserProfile['name'],
            );

            // set view variables
            $this->view->name     = $fbUserProfile['name'];

            return $data;
        }
    }

    private function _getFbLoginUrl()
    {
        $fbLoginParams = array('redirect_uri' => $this->getReturnPath('/auth/fb?redirect_uri=' . $this->_redirectUri));
        return $this->_fb->getLoginUrl($fbLoginParams);
    }
}