<?php

class User_Model_Task extends Mg_Data_Object
{
    public function canDelete()
    {
        return $this->total_hours == 0;
    }

    public function delete()
    {
        $userTasks = User_Model_Tasks::getInstance();
        $userTasks->delete($this);

        // get parent task
        $tasks = App_Model_Tasks::getInstance();
        $task  = $tasks->find($this->task_id);

        // delete parent task if un-used
        if ($task->canDelete()) {
            $tasks->delete($task);
        }
    }

    public static function create($data)
    {
        // create and save new task
        $task = new App_Model_Task($data);
        App_Model_Tasks::getInstance()->insert($task);

        // create and save user task
        $userTaskData = array_merge($data, array('task_id' => $task->id));
        $userTask = new self($userTaskData);
        User_Model_Tasks::getInstance()->insert($userTask);

        return $userTask;
    }
}