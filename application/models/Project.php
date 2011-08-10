<?php
class App_Model_Project extends Mg_Data_Object
{
    public function canDelete()
    {
        return $this->project_total_hours == 0;
    }

    public static function create($data)
    {
        $project = new self($data);

        App_Model_Projects::getInstance()->insert($project);

        return $project;
    }
}