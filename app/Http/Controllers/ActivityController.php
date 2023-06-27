<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityPostRequest;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function index()
    {
        $activities = Activity::all();
        //return $tags;
        return response()->json([
            'success' => true,
            'data' => $activities,
            'message' => 'Lista de Actividades'
        ], 200);
    }

    public function show(Activity $activity)
    {
        $activity = Activity::find($activity);
        return response()->json($activity);
    }

    public function store(ActivityPostRequest $request)
    {

        $image = $this->saveImage($request->image, 'activities');

        $activity = Activity::create([
            'body' => $request['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);

        return response()->json([
            'success' => true,
            'data' => $activity,
            'message' => "record saved successfully!",
            //'name' => $activity
        ], 200);
    }

    public function update(ActivityPostRequest $request, $id)
    {
        $input = $request->all();
        $activity = Activity::find($id);
        $imagePath = str_replace(url('/storage'), '', $activity->image);

        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $newImage = $request->file('image');
            $newImagePath = $newImage->store('activities', 'public');

            // Actualizar el campo 'image' con la nueva ruta de la imagen
            $activity->image = $newImagePath;
        }

        Log::channel('stderr')->info($request);
        $activity->body = $input['body'];
        $activity->save();

        return response()->json([
            'success' => true,
            'data' => Activity::all(),
            'message' => "record updated successfully!",
            //'name' => $activity
        ], 200);
    }

    public function destroy(Activity $activity)
    {

        $imagePath = str_replace(url('/storage'), '', $activity->image);
        Storage::disk('public')->delete($imagePath);

        $activity->delete();

        return response()->json([
            'success' => true,
            'data' => $imagePath,
            'message' => "Registro eliminado correctamente",
        ], 200);
    }
}
