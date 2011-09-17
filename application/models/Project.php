<?php
class App_Model_Project extends App_Model_Abstractable
{
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->title;
    }

    public function canDelete()
    {
        return $this->total_users == 0;
    }

    public function delete()
    {
        return App_Model_Projects::getInstance()->delete($this);
    }

    public static function create(array $data)
    {
        $project = new self($data);

        App_Model_Projects::getInstance()->insert($project);

        return $project;
    }
}