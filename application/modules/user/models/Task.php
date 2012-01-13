<?php

class User_Model_Task extends App_Model_Task
{
    public function getId()
    {
        return $this->task_id;
    }

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
            $task->delete();
        }
    }

    public static function create(array $data)
    {
        // create and save new task
        $task = parent::create($data);

        // create and save user task
        $userTaskData = array_merge($data, array('task_id' => $task->id));
        $userTask = new self($userTaskData);
        User_Model_Tasks::getInstance()->insert($userTask);

        return $userTask;
    }

    public function update(array $data)
    {
        // update user task
        $this->setFromArray($data);
        $updated = User_Model_Tasks::getInstance()->update($this);

        // get and update original task
        $task = App_Model_Tasks::getInstance()->find($this->task_id);
        return $task->update($data) && $updated;
    }

    public function setColor($newColor)
    {
        $tasks = User_Model_Tasks::getInstance();
        $tasks->setUserId($this->user_id);

        // find user task with same color
        $conflictingTask = $tasks->findByColor($newColor);
        if ($conflictingTask) {
            // swap colors
            $conflictingTask->color = $this->color;
            $tasks->update($conflictingTask);
        }

        // update task
        $this->color = $newColor;
    }
}