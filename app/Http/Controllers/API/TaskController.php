<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TodoList $todolist)
    {
        $task =  Task::withWhereHas("todolist", function ($query) use ($todolist) {
            $query->where("id", $todolist->id);
        })->get();

        return \response($task, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TodoList  $todolist
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTaskRequest $request, TodoList $todolist)
    {
        $data = $request->validated();
        $data["todo_list_id"]  = $todolist->id;
        $task = Task::create($data);

        return \response()->json($task, Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::find($id);
        \abort_if(!$task, 404, "Task not found");
        $task->fill($request->validated())->saveOrFail();
        return \response()->json($task, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::find($id);
        \abort_if(!$task, 404, "Task not found");

        $task->delete();

        return \response()->noContent();
    }
}
