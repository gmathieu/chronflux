<?php
class App_Model_Projects extends Mg_Data_Service
{
    public function init()
    {
        parent::init();

        // get total hours associated with project
        $this->select->joinLeft('projects_total_users',
                        'projects.id = projects_total_users.project_id');
    }
}