<?php

require_once('AbstractDatabaseTestCase.php');

class UserTasksTest extends AbstractDatabaseTestCase
{
    public $tasks;

    public function setUp()
    {
        parent::setUp();

        $this->tasks = User_Model_Tasks::getInstance();
        $this->tasks->setUserId(null);
    }

    public function testFetchAllByUser()
    {
        $this->tasks->setUserId(USER_JOHN);
        $taskSet = $this->tasks->fetchAll();

        $this->assertTrue(count($taskSet) > 0);

        $expectedTasks = array(PROJECT_WEBSITE, PROJECT_ECOMMERCE, TASK_CLIENT_MEETING);
        foreach ($taskSet as $task) {
            $this->assertEquals(USER_JOHN, $task->user_id);
            $this->assertTrue(in_array($task->task_id, $expectedTasks));
        }
    }

    public function testCreate()
    {
        $data = array(
            'user_id'      => USER_JOHN,
            'name'         => 'Ping pong',
            'abbreviation' => 'ping',
            'description'  => 'Killing it at ping pong',
            'color'        => '56G8AF',
        );

        $userTask = User_Model_Task::create($data);
        $task  = App_Model_Tasks::getInstance()->findByName($data['name']);

        $this->assertInstanceOf('App_Model_Task', $task);
        $this->assertEquals($data['name'], $task->name);
        $this->assertEquals($data['abbreviation'], $task->abbreviation);
        $this->assertEquals($data['description'], $task->description);

        $this->assertEquals($task->id, $userTask->task_id);
        $this->assertEquals(USER_JOHN, $userTask->user_id);
        $this->assertEquals($data['color'], $userTask->color);
    }

    /**
     * @expectedException Exception
     */
    public function testReorderException()
    {
        $this->tasks->reorder(array());        
    }

    public function testReorder()
    {
        $newOrder = array(
            TASK_CLIENT_MEETING,
            TASK_BACK_END,
            TASK_FRONT_END,
        );

        $this->tasks->setUserId(USER_JOHN);
        $this->tasks->reorder($newOrder);

        $taskSet = $this->tasks->fetchAll();
        $index   = 0;
        foreach ($taskSet as $task) {
            $this->assertEquals($newOrder[$index], (int)$task->id);
            $index++;
        }
    }

    public function testCanDelete()
    {
        $this->tasks->setUserId(USER_JOHN);
        $taskWithJobs = $this->tasks->findByTaskId(TASK_FRONT_END);
        $this->assertFalse($taskWithJobs->canDelete());

        $this->tasks->setUserId(USER_JOHN);
        $taskWithoutJobs = $this->tasks->findByTaskId(TASK_CLIENT_MEETING);
        $this->assertTrue($taskWithoutJobs->canDelete());

        $this->tasks->setUserId(USER_JEN);
        $taskWithoutJobs = $this->tasks->findByTaskId(TASK_CLIENT_MEETING);
        $this->assertTrue($taskWithoutJobs->canDelete());
    }

    /**
     * @depends testCanDelete
     */
    public function testDelete()
    {
        $tasks = App_Model_Tasks::getInstance();

        // Delete USER_JOHN's TASK_CLIENT_MEETING
        $this->tasks->setUserId(USER_JOHN);
        $task = $this->tasks->findByTaskId(TASK_CLIENT_MEETING);
        $task->delete();

        // check that USER_JOHN's TASK_CLIENT_MEETING is deleted
        $this->assertTrue(is_null($this->tasks->findByTaskId(TASK_CLIENT_MEETING)));

        // TASK_CLIENT_MEETING should still exist
        $this->assertInstanceOf('App_Model_Task', $tasks->find(TASK_CLIENT_MEETING));

        // Delete USER_JEN's TASK_CLIENT_MEETING
        $this->tasks->setUserId(USER_JEN);
        $task = $this->tasks->findByTaskId(TASK_CLIENT_MEETING);
        $task->delete();

        // TASK_CLIENT_MEETING be deleted
        $this->assertNull($tasks->find(TASK_CLIENT_MEETING));
    }
}