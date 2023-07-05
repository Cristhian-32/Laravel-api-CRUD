<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecordPostRequest;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $records = Record::all();
        //return $tags;
        return response()->json([
            'success' => true,
            'data' => $records,
            'message' => 'Todas las Asistencias'
        ], 200);
    }

    public function show(Record $record)
    {
        $record = Record::find($record);
        return response()->json($record);
    }

    public function store(RecordPostRequest $request)
    {

        $evidence_image = $this->saveImage($request->evidence, 'evidences');
        $date_now = Carbon::now()->toDateString();
        $time_now = Carbon::now()->toTimeString();

        $record = Record::create([
            'user_id' => auth()->user()->id,
            'activity_id' => $request['activity_id'],
            'name' => $request['name'],
            'code' => $request['code'],
            'school' => $request['school'],
            'level' => $request['level'],
            'evidence' => $evidence_image,
            'date' => $date_now,
            'time' => $time_now

        ]);

        return response()->json([
            'success' => true,
            'data' => Record::all(),
            'message' => "record saved successfully!",
            //'name' => $activity
        ], 200);
    }

    public function update(RecordPostRequest $request, $id)
    {
        $input = $request->all();
        $record = Record::find($id);
        $imagePath = str_replace(url('/storage'), '', $record->evidence);

        if ($request->hasFile('evidence')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $newImage = $request->file('evidence');
            $newImagePath = $newImage->store('evidences', 'public');

            // Actualizar el campo 'image' con la nueva ruta de la imagen
            $record->evidence = $newImagePath;
        }

        Log::channel('stderr')->info($request);
        $record->name = $input['name'];
        $record->code = $input['code'];
        $record->school = $input['school'];
        $record->level = $input['level'];
        $record->body = $input['body'];
        $record->save();

        return response()->json([
            'success' => true,
            'data' => Record::all(),
            'message' => "record updated successfully!",
            //'name' => $activity
        ], 200);
    }

    public function destroy(Record $record)
    {

        $imagePath = str_replace(url('/storage'), '', $record->evidence);
        Storage::disk('public')->delete($imagePath);

        $record->delete();

        return response()->json([
            'success' => true,
            'data' => $record,
            'message' => "Registro eliminado correctamente",
        ], 200);
    }
}
