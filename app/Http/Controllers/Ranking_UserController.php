<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ranking_User;
use App\Models\Ranking;
use App\Models\User;

class Ranking_UserController extends Controller
{
    public function insert(Request $request, $id_alumno)
    {
        $user = $request->user();

        if (isset($user->rol) && $user->rol == "profesor") {
            $alumno = User::find($id_alumno);

            if ($alumno) {
                $ranking_user = new Ranking_User();
                $ranking_user->id_user = $alumno->id;
                $ranking_user->user_name = $alumno->nick;
                $ranking_user->points = 0;

                if ($ranking_user->save()) {
                    return response()->json(['success' => true, 'message' => 'Alumno agregado correctamente']);
                } else {
                    return response()->json(['success' => false, 'message' => 'No se pudo agregar el alumno al ranking']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontró al alumno']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para realizar esta acción']);
        }
    }
    public function kickoff(Request $request, $id_alumno)
    {
        $user = $request->user();

        if (isset($user->rol) && $user->rol == "profesor") {
            $student = Ranking_User::where('id_ranking', $id_alumno)->first();

            if ($student) {
                $student->delete();
                return response()->json(['message' => 'El alumno ha sido eliminado del ranking']);
            } else {
                return response()->json(['message' => 'El alumno no ha sido encontrado en el ranking'], 404);
            }
        } else {
            return response()->json(['message' => 'No tiene permisos para realizar esta acción'], 403);
        }
    }

    public function update_points(Request $request, $id_alumno)
    {
        $user = $request->user();

        if (isset($user->rol) == "profesor") {

            $student = Ranking_User::where('id_user', $id_alumno)->first();

            if ($student) {

                $student->points = $request->points;
                $student->save();

                return response()->json(['message' => 'Los puntos del estudiante ' . $student->user_name . ' han sido actualizados'], 200);
            } else {

                return response()->json(['message' => 'El estudiante ' . $student->user_name . ' no ha sido encontrado'], 404);
            }
        } else {

            return response()->json(['message' => 'No tienes permisos para actualizar los puntos'], 401);
        }
    }

    public function show_students(Request $request)
    {
        $students = User::where('rol', 'student')->orderBy('points', 'desc')->get();
        return response()->json($students);
    }
}
