<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLabelRequest;
use App\Http\Requests\UpdateLabelRequest;
use App\Models\Label;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LabelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $label = Label::where("user_id", $request->user()->id)->get();
        return $label;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateLabelRequest $request)
    {
        $data = $request->validated();
        $data["user_id"] = $request->user()->id;
        $label = Label::create($data);
        return \response()->json($label, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLabelRequest $request, $id)
    {
        $label =  Label::find($id);
        \abort_if(!$label, Response::HTTP_NOT_FOUND, "Label not found.");

        $label->fill($request->all())
            ->saveOrFail();

        return \response()->json($label, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): JsonResponse
    {
        $label = Label::find($id);
        \abort_if(!$label, Response::HTTP_NOT_FOUND, "Label not found");

        $label->delete();
        return \response()->json(["message" => "label berhasil dihapus"], Response::HTTP_OK);
    }
}
