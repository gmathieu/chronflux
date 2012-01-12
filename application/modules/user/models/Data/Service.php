<?php
class User_Model_Data_Service extends Mg_Data_Service
{
    protected $_dbTableRowClass = false;
    protected $_userId;

    public function init()
    {
        parent::init();

        $this->select->order('order');
    }

    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    public function reorder(array $ids)
    {
        $this->_requireUserId();

        $orderCount = 0;
        foreach ($ids as $id) {
            $userObj = $this->find($this->_userId, $id);
            $userObj->order = ++$orderCount;
            self::update($userObj);
        }
    }

    protected function _requireUserId()
    {
        // chekc that user ID is set
        if (!$this->_userId) {
            throw new Exception("setUserId() must be called before using this function.");
        }
    }
}