<?php

class User_Form_Project extends App_Form
{
    public function init()
    {
        parent::init();

        $this->addHiddenElement('user_id');

        $this->addTextElement('title', array(
            'label'     => 'Title',
            'required'  => true,
            'autofocus' => true,
        ));

        $this->addTextElement('subtitle', array(
            'description' => 'Ex. client name, project code…',
            'label'       => 'Subtitle',
        ));

        $this->addTextareaElement('description', array(
            'label' => 'Description',
        ));

        $this->addSubmitElement('Save');
    }
}