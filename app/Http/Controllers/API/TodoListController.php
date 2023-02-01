<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostTodoListRequest;
use App\Http\Requests\UpdateTodoListRequest;
use App\Http\Resources\API\V1\TodoListResource;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class TodoListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list =  TodoList::with("user")->get();
        return response()->json([
            "data" => TodoListResource::collection($list)
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostTodoListRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $list = TodoList::create($data);
        return \response()->json([
            "data" => TodoListResource::make($list)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $list = TodoList::findOrFail($id);
        return \response()->json([
            "data" => TodoListResource::make($list)
        ], Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTodoListRequest $request, $id)
    {
        $list = TodoList::findOrFail($id);
        $list->fill($request->all())
            ->saveOrFail();

        return \response()->json([
            "data" => TodoListResource::make($list)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = TodoList::destroy($id);
        if (!$deleted) {
            \abort(404, "Todolist not found");
            return;
        }
        return \response()->noContent();
    }
}
