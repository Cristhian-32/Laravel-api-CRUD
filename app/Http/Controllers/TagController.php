<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagPostRequest;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{

    public function index()
    {
        $tags = Tag::all();
        //return $tags;
        return response()->json($tags);
    }

    public function show(Tag $tag)
    {
        $tag = Tag::find($tag);
        return response()->json($tag);
    }

    public function store(TagPostRequest $request)
    {
        $tag = Tag::create($request->all());

        return response()->json([
            'message' => "record saved successfully!",
            'name' => $tag
        ], 200);
    }

    public function update(TagPostRequest $request, tag $tag)
    {
        $tag->update($request->all());

        return response()->json([
            'message' => "record updated successfully!",
            'name' => $tag
        ], 200);
    }

    public function destroy(tag $tag)
    {
        $tag->delete();
        return response()->json([
            'message' => "record deleted successfully!",
        ], 200);
    }
}
