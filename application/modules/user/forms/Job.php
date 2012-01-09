<?php

class User_Form_Job extends App_Form
{
    public function init()
    {
        parent::init();

        $this->addHiddenElement('user_id', array(
            'filter'   => 'Digits',
            'required' => true,
        ));

        $this->addHiddenElement('project_id', array(
            'filter'   => 'Digits',
            'required' => true,
        ));

        $this->addHiddenElement('task_id', array(
            'filter'   => 'Digits',
            'required' => true,
        ));

        $this->addHiddenElement('date', array(
            'required' => true,
            'validate' => array('Date')
        ));

        $this->addHiddenElement('start_time', array(
            'required' => true,
            'validate' => array('Float')
        ));

        $this->addHiddenElement('stop_time', array(
            'required' => true,
            'validate' => array('Float')
        ));
    }
}