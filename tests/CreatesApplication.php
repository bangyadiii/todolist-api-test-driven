<?php

namespace Tests;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    protected $task;
    protected $list;



    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
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
