<?php

abstract class App_Model_Abstractable extends Mg_Data_Object
{
    abstract public function getId();
    abstract public function getName();
    abstract public function canDelete();
    abstract public function update(array $data);
    abstract public function delete();

    abstract public static function create(array $data);
}