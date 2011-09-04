<?php

class User_Model_Projects extends User_Model_Data_Service
{
    public function init()
    {
        parent::init();

        // get actual project
        $this->select->joinLeft('projects',
                                'projects.id = user_projects.project_id');

        // default order
        $this->select->order('projects.title');
    }

    public function fetchByDateOrActive($date)
    {
        $this->_requireUserId();

        // total hours join statement
        $join = $this->adapter->quoteInto('user_projects.project_id = jobs_total_hours_by_date.project_id' .
                                          ' and jobs_total_hours_by_date.user_id = ?', $this->_userId);
        // get total hours by date
        $this->select->joinLeft('jobs_total_hours_by_date',
                                $join,
                                array('total_hours' => new Zend_Db_Expr('ifnull(total_hours_by_date,0)')))
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
            // get total hours associated
            $join = $this->adapter->quoteInto('user_projects.project_id = user_projects_total_hours.project_id ' .
                                     'and user_projects_total_hours.user_id = ?', $this->_userId);
            $this->select->joinLeft('user_projects_total_hours', $join);
            $this->select->where('user_projects.user_id = ?', $this->_userId);
        }

        return parent::fetchAll();
    }
}