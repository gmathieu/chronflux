<?php

class App_Model_Tasks extends Mg_Data_Service
{
    public function init()
    {
        parent:: init();

        // get total associated user tasks
        $this->select->joinLeft('tasks_total_users',
                                'tasks.id = tasks_total_users.task_id',
                                array('total_users'));
    }
}