<?php

namespace Tests;

use App\Models\Label;
use App\Models\Task;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public User $authUser;
    protected Task $task;
    protected TodoList $list;

    protected function createLabel($args = [])
    {
        if (isset($args)) {
            return Label::factory()->create($args);
        }

        return Label::factory()->create();
    }

    protected function createAuthUser($args = [])
    {
        if (isset($args)) {
            $this->authUser = User::factory()->create($args);
        }

        $this->authUser =  User::factory()->create();
    }

    protected function createTask($args = [])
    {
        if (isset($args)) {
            return Task::factory()->create($args);
        }

        return Task::factory()->create();
    }

    protected function createTodo($args = [])
    {
        if (isset($args)) {
            return TodoList::factory()->create($args);
        }

        return TodoList::factory()->create();
    }
}
