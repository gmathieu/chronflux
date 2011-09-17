<?php
require_once('AbstractDatabaseTestCase.php');
class TasksTest extends AbstractDatabaseTestCase
{
    public $tasks;

    public function setUp()
    {
        parent::setUp();
        $this->tasks = App_Model_Tasks::getInstance();
    }

    public function testCanDelete()
    {
        $usedTask = $this->tasks->find(TASK_USED);
        $this->assertFalse($usedTask->canDelete());

        $unusedTask = $this->tasks->find(TASK_UNUSED);
        $this->assertTrue($unusedTask->canDelete());
    }

    public function testAbstractMethods()
    {
        $task = $this->tasks->find(TASK_USED);

        $this->assertEquals($task->id, $task->getId());
        $this->assertEquals($task->name, $task->getName());
    }
}