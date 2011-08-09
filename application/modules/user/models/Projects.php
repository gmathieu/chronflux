<?php

class User_Model_Projects extends Mg_Data_Service
{
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
        $this->select->where('user_projects.user_id = ?', $userId);
    }

    public function fetchByDateOrActive($date)
    {
        // total hours join statement
        $join = $this->adapter->quoteInto('user_projects.project_id = jobs_total_hours_by_date.project_id' .
                                          ' and jobs_total_hours_by_date.user_id = ?', $this->_userId);
        // get total hours by date
        $this->select->joinLeft('jobs_total_hours_by_date',
                                $join, array('total_hours_by_date'))
                     ->where("`date` = ? AND `total_hours_by_date` > 0 OR `active` = 1", $date);

        return $this->fetchAll();
    }
}