<?php

class App_User_Controller_Settings extends App_User_Controller_Action
{
    public $dataObjName;
    public $dataService;
    public $userDataSet;

    public function init()
    {
        parent::init();

        $this->view->headLink()->appendStylesheet($this->view->baseUrl('css/settings.css'));

        if ('projects' === $this->controllerName || 'tasks' === $this->controllerName) {
            // init data object info
            $this->dataObjName = ucfirst(trim($this->controllerName, 's'));
    
            // init data service based on controller name
            $dataServiceObj    = "User_Model_{$this->dataObjName}s";
            $this->dataService = $dataServiceObj::getInstance();
            $this->dataService->setUserId($this->user->id);
    
            // get data set
            $this->userDataSet = $this->dataService->fetchAll();
        }
    }

    public function preDispatch()
    {
        parent::preDispatch();

        // disable automatic render to wrap content in settings layout
        if (!$this->isAjax()) {
            $this->getHelper('viewRenderer')->setNoRender();
        }

        // setup view variables
        $this->view->dataObjName      = $this->dataObjName;
        $this->view->userDataSet      = $this->userDataSet;
        $this->view->selectedMenuItem = 'settings';
    }

    public function postDispatch()
    {
        parent::postDispatch();

        // capture current content
        $this->view->content = $this->view->render("{$this->controllerName}/{$this->actionName}.phtml");

        // render settings menu
        $this->getHelper('viewRenderer')->renderScript('settings/_layout.phtml');
    }

    public function listAction()
    {
    }

    public function newAction()
    {
        $form = $this->_initForm();

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            $userDataObjName = "User_Model_{$this->dataObjName}";
            $userDataObj     = $userDataObjName::create($this->_request->getPost());

            return $this->_redirectToEditAction($userDataObj);
        }
    }

    public function editAction()
    {
        $userDataObj = $this->_requireDataObj();
        $form        = $this->_initForm();

        // pre-populate form
        $form->populate($userDataObj->getRawData());

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            // update user project with POST data
            $userDataObj->setFromArray($this->_request->getPost());
            $this->dataService->update($userDataObj);
        }

        // setup inline editing helper with proper data
        $this->view->inlineEditing()->setForm($form)->setDataObject($userDataObj);

        // set view variables
        $this->view->deleteUrl   = $this->_getDeleteUrl($userDataObj);
        $this->view->userDataObj = $userDataObj;
    }

    public function deleteAction()
    {
        $userDataObj = $this->_requireDataObj();
        $userDataObj->delete();

        return $this->_redirect("user/{$this->user->username}/{$this->controllerName}");
    }

    protected function _requireDataObj()
    {
        $id          = $this->_getParam('id');
        $findMethod  = "findBy{$this->dataObjName}Id";
        $userDataObj = $this->dataService->$findMethod($id);

        if ($userDataObj) {
            return $userDataObj;
        } else {
            throw new Exception("{$this->dataObjName} {$id} not found.");
        }
    }

    protected function _redirectToEditAction($dataObj)
    {
        return $this->_redirect($this->_getRedirectUrl($dataObj));
    }

    protected function _getRedirectUrl($dataObj)
    {
        return "user/{$this->user->username}"
               . "/{$this->controllerName}/edit"
               . "/id/{$dataObj->getId()}";
    }

    private function _initForm()
    {
        $formName = "User_Form_{$this->dataObjName}";
        $form = new $formName;

        // force user_id
        $form->user_id->setValue($this->user->id);

        $this->view->form = $form;

        return $form;
    }

    private function _getDeleteUrl($userDataObj)
    {
        return "user/{$this->user->username}"
               . "{$this->controllerName}/delete"
               . "/id/{$userDataObj->getId()}";
    }
}