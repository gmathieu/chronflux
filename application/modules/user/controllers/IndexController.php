<?php

class User_IndexController extends App_User_Controller_Settings
{
    public function editAction()
    {
        // pre-popuplate form
        $form = new User_Form_User();
        $form->populate($this->user->getRawData());

        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();

            // form validation
            if ($form->isValid($post)) {

                // create user object
                $users = App_Model_Users::getInstance();
                $this->user->setFromArray($form->getValues());
                $users->update($this->user);

                // check to see if session user is the same
                if ($this->session->user->id == $user->id) {
                    $this->session->user = $this->user;
                }

                $this->_redirector->gotoUrl("user/{$this->user->username}/edit");
            }
        }

        // setup inline editing helper with proper data
        $this->view->inlineEditing()->setForm($form)->setDataObject($this->user);
    }
}