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
            'description' => 'An abbreviated representation of the above name used across the application. '
                             . 'Should be no more than '
                             . App_Model_Task::MAX_ABBR_CHARS . ' characters long.',
            'label'       => 'Abbreviation',
            'maxlength'   => App_Model_Task::MAX_ABBR_CHARS,
            'required'    => true,
            'validators'  => array($stringLength),
        ));

        // TODO: enable personalization
        $colors = array(
            'A7321C' => 'A7321C',
            'FFDC68' => 'FFDC68',
            'CC982A' => 'CC982A',
            '928941' => '928941',
            '352504' => '352504',
        );
        $this->addRadioElement('color', array(
            'filter'       => 'Alnum',
            'label'        => 'Color',
            'multiOptions' => $colors,
            'required'     => true,
            'separator'    => ' ',
        ));

        $this->addTextareaElement('description', array(
            'label' => 'Description',
        ));

        $this->addSubmitElement('Save');
    }
}