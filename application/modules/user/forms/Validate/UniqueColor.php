<?php
class User_Form_Validate_UniqueColor extends Zend_Validate_Abstract
{
    const EXSISTS = 'exists';

    protected $_messageTemplates = array(
        self::EXSISTS => 'This color is already used by another task.'
    );

    public function isValid($value, $context = null)
    {
        $tasks = User_Model_Tasks::getInstance();
        $tasks->setUserId($context['user_id']);

        // break validation when the color is already taken
        if (0 == count($tasks->findByColor($value))) {
            return true;
        } else {
            $this->_error(self::EXSISTS);
            return false;
        }
    }
}
?>