<?php

class User_Model_Tasks extends User_Model_Data_Service
{
    public function init()
    {
        parent::init();

        // get actual task
        $this->select->joinLeft('tasks', 'tasks.id = user_tasks.task_id')
                     ->joinLeft('users', 'users.id = user_tasks.user_id', array('username'))
                     ->order('user_tasks.order');
    }

    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    public function fetchAll()
    {
        // filter by user ID
        if ($this->_userId) {
            // get total hours associated
            $join = $this->adapter->quoteInto('user_tasks.task_id = user_tasks_total_hours.task_id ' .
                                     'and user_tasks_total_hours.user_id = ?', $this->_userId);
            $this->select->joinLeft('user_tasks_total_hours',
                                    $join,
                                    array('total_hours' => new Zend_Db_Expr('ifnull(total_hours,0)')));
            $this->select->where('user_tasks.user_id = ?', $this->_userId);
        }

        return parent::fetchAll();
    }

    public function reorder(array $taskIds)
    {
        $this->_requireUserId();

        $orderCount = 0;
        foreach ($taskIds as $taskId) {
            $userTask = $this->find($this->_userId, $taskId);
            $userTask->order = ++$orderCount;
            self::update($userTask);
        }
    }
}