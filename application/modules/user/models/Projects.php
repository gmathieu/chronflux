<?php

class User_Model_Projects extends Mg_Data_Service
{
    protected $_dbTableRowClass = false;

    private $_userId;

    public function init()
    {
        parent::init();

        // get actual project
        $this->select->joinLeft('projects',
                                'projects.id = user_projects.project_id');
        // default order
        $this->select->order('projects.name');
    }

    public function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    public function fetchByDateOrActive($date)
    {
        $this->_requireUserId();

        // total hours join statement
        $join = $this->adapter->quoteInto('user_projects.project_id = jobs_total_hours_by_date.project_id' .
                                          ' and jobs_total_hours_by_date.user_id = ?', $this->_userId);
        // get total hours by date
        $this->select->joinLeft('jobs_total_hours_by_date',
                                $join, array('total_hours_by_date'))
                     ->where("`date` = ? AND `total_hours_by_date` > 0 OR `active` = 1", $date);

        return $this->fetchAll();
    }

    public function fetchInactive()
    {
        return $this->fetchByActive(User_Model_Project::INACTIVE);
    }

    public function fetchAll()
    {
        // filter by user ID
        if ($this->_userId) {
            $this->select->where('user_projects.user_id = ?', $this->_userId);
        }

        return parent::fetchAll();
    }

    private function _requireUserId()
    {
        // chekc that user ID is set
        if (!$this->_userId) {
            throw new Exception("setUserId() must be called before using this function.");
        }
    }
}