<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsistenciaPostRequest;
use App\Models\Asistencia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsistenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $asistencias = Asistencia::all();
        //return $tags;
        return response()->json([
            'success' => true,
            'data' => $asistencias,
            'message' => 'Todas las Asistencias'
        ], 200);
    }

    public function show(Asistencia $asistencia)
    {
        $asistencia = Asistencia::find($asistencia);
        return response()->json($asistencia);
    }

    public function store(AsistenciaPostRequest $request)
    {

        $date_now = Carbon::now()->toDateTimeString();

        $asistencia = Asistencia::create([
            'user_id' => auth()->user()->id,
            'activity_id' => $request['activity_id'],
            'code' => $request['code'],
            'level' => $request['level'],
            'date' => $date_now,

        ]);

        return response()->json([
            'success' => true,
            'data' => $asistencia,
            'message' => "record saved successfully!",
            //'name' => $activity
        ], 200);
    }

    public function update(AsistenciaPostRequest $request, $id)
    {
        $input = $request->all();
        $asistencia = Asistencia::find($id);
        Log::channel('stderr')->info($request);
        $asistencia->code = $input['code'];
        $asistencia->level = $input['level'];
        $asistencia->save();

        return response()->json([
            'success' => true,
            'data' => Asistencia::all(),
            'message' => "record updated successfully!",
            //'name' => $activity
        ], 200);
    }

    public function destroy(Asistencia $asistencia)
    {
        $asistencia->delete();
        return response()->json([
            'success' => true,
            'data' => $asistencia,
            'message' => "Registro eliminado correctamente",
        ], 200);
    }
}
