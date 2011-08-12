<?php
class User_Model_Data_Service extends Mg_Data_Service
{
    protected $_dbTableRowClass = false;
    protected $_userId;

    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    protected function _requireUserId()
    {
        // chekc that user ID is set
        if (!$this->_userId) {
            throw new Exception("setUserId() must be called before using this function.");
        }
    }
}