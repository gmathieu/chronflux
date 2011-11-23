<?php

class User_Form_User extends App_Form
{
    public function init($userId = false)
    {
        parent::init();

        // setup validator params
        $noRecordExistsParams = array('table' => 'users', 'field' => 'username');

        // exclude user ID on update
        if ($userId) {
            $noRecordExistsParams['exclude'] = array('field' => 'id', 'value' => $userId);
        }

        $this->addTextElement('username', array(
            'label'      => 'Username',
            'required'   => true,
            'autofocus'  => true,
            'filters'    => array('StringToLower'),
            'validators' => array(
                array('Db_NoRecordExists', false, $noRecordExistsParams),
                array('regex', false, '/^[0-9a-zA-Z_\-\.]+$/i'),
            ),
        ));

        $this->addDropDownElement('clock_in_at', array(
            'description'  => 'What time do you usually start working?',
            'filters'      => array('digits'),
            'label'        => 'Clock in at',
            'multiOptions' => $this->_getHours(),
        ));

        $this->addSubmitElement('Save');
    }

    private function _getHours()
    {
        $data = array();

        for ($hour = 0; $hour < 24; $hour++) {
            $amPm = ($hour < 12) ? 'am' : 'pm';
            $label = ($hour < 12) ? $hour : $hour - 12;

            // make sure 0 shows up as 12
            if ($label == 0) {
                $label = 12;
            }

            $data[$hour] = "{$label} {$amPm}";
        }

        return $data;
    }
}