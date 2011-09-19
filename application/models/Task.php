<?php

class App_Model_Task extends App_Model_Abstractable
{
    const MAX_ABBR_CHARS = 5;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function canDelete()
    {
        return $this->total_users == 0;
    }

    public function delete()
    {
        App_Model_Tasks::getInstance()->delete($this);
    }

    public static function create(array $data)
    {
        $task = new self($data);

        App_Model_Tasks::getInstance()->insert($task);

        return $task;
    }
}