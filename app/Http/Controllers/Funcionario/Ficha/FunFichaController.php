<?php

namespace App\Http\Controllers\Funcionario\Ficha;

use App\Models\User;
use App\Models\Ficha;
use App\Models\Paciente;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FunFichaController extends Controller
{
    //TODO: Crear nueva ficha médica para el paciente
    public function crearFicha(Request $request)
    {
        $validacion_datos = $request->validate([
            'id_paciente' => 'required|max:30|min:1',
            'receta' => 'required|max:60|min:3'
        ], [
            'id_paciente.required' => 'El campo id_paciente es requerido.',
            'receta.required' => 'El campo receta es requerido.'
        ]);

        $usuario = auth()->user();
        $paciente = Paciente::find($request->id_paciente);

        if (!$paciente) {
            return response()->json(['error' => 'El paciente especificado no existe'], 404);
        }
        if ($usuario->roles !== "Funcionario" || $usuario->estado !== "Habilitado") {
            return response()->json(['error' => 'Solo los funcionarios que se encuentren habilitados pueden crear fichas médicas'], 403);
        }

        $ficha = $validacion_datos;
        $ficha['id_usuario'] = $usuario->id;
        $ficha = Ficha::create($ficha);
        return response()->json([
            'message' => 'Ficha médica registrada exitosamente',
            'ficha' => $ficha
        ], 200);
    }

    //TODO: Obtener todas las fichas médicas
    public function obtenerFichas(Request $request)
    {
        $fichas = Ficha::all();
        if ($fichas->count() > 0) {
            return response()->json([
                'fichas' => $fichas
            ], 200);
        } else {
            return response()->json([
                'message' => 'No hay fichas médicas registradas.'
            ], 200);
        }
    }

    //TODO: Buscar fichas médicas
    public function buscarFicha(Request $request, $id)
    {
        $ficha = Ficha::find($id);
        if ($ficha) {
            return response()->json([
                'message' => 'Ficha médica encontrada',
                'Ficha' => $ficha
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ficha médica no encontrada'
            ], 404);
        }
    }

    //TODO: Modificar ficha médica
    public function modificarFicha(Request $request, $id)
    {
        $validacion_datos = $request->validate([
            'id_paciente' => 'required|max:30|min:1',
            'receta' => 'required|max:60|min:3'
        ], [
            'id_paciente.required' => 'El campo id_paciente es requerido.',
            'receta.required' => 'El campo receta es requerido.'
        ]);

        $usuario = auth()->user();
        $paciente = Paciente::find($request->id_paciente);
        $ficha = Ficha::find($id);

        if (!$ficha) {
            return response()->json(['error' => 'La ficha médica no existe'], 404);
        }

        if (!$paciente) {
            return response()->json(['error' => 'El paciente especificado no existe'], 404);
        }

        if ($usuario->roles !== "Funcionario" || $usuario->estado !== "Habilitado") {
            return response()->json(['error' => 'Solo los funcionarios que se encuentren habilitados pueden modificar fichas médicas'], 403);
        }


        $ficha->update($validacion_datos);
        return response()->json(['message' => 'Ficha médica modificada exitosamente', 'ficha' => $ficha], 200);
    }

    //TODO: Eliminar ficha médica
    public function eliminarFicha($id)
    {
        $ficha = Ficha::find($id);
        if (!$ficha) {
            return response()->json(['error' => 'La ficha médica no existe'], 404);
        }
        $ficha->delete();
        return response()->json(['message' => 'Ficha médica eliminada exitosamente'], 200);
    }
}
