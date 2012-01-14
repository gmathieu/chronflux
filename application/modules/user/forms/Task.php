<?php

class User_Form_Task extends App_Form
{
    public function init()
    {
        parent::init();

        $this->addHiddenElement('user_id');

        $this->addTextElement('name', array(
            'description' => 'Ex. Client meeting, Cleaning the dishes',
            'label'       => 'Name',
            'required'    => true,
            'autofocus'   => true,
        ));

        $stringLength = array(
            'stringLength',
            false,
            array(0, App_Model_Task::MAX_ABBR_CHARS)
        );
        $this->addTextElement('abbreviation', array(
            'description' => 'An abbreviated representation of the above name used across the application.'
                             . 'Should be no more than '
                             . App_Model_Task::MAX_ABBR_CHARS . ' characters long.',
            'label'       => 'Abbreviation',
            'maxlength'   => App_Model_Task::MAX_ABBR_CHARS,
            'required'    => true,
            'validators'  => array($stringLength),
        ));

        $this->addTextElement('color', array(
            'description'  => 'Be sure to chose a color that stands out from existing tasks',
            'filter'       => array('Alnum', 'StringToUpper'),
            'label'        => 'Color',
            'required'     => true,
        ));

        $this->addTextareaElement('description', array(
            'label' => 'Description',
        ));

        $this->addSubmitElement('Save');
    }
}