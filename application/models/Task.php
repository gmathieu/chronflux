<?php

class App_Model_Task extends Mg_Data_Object
{
    public function canDelete()
    {
        return $this->total_users == 0;
    }
}