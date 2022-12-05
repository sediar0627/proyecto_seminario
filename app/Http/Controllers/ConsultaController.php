<?php

namespace App\Http\Controllers;

use App\Enums\ConsultaEstado;
use App\Models\Camara;
use App\Models\Consulta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConsultaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'placa' => [
                'required', 
                'string', 
                ('regex:'.Consulta::regexPlacasVehiculares())
            ],
            'camara' => [
                'required', 
                'string', 
                'exists:camaras,serial'
            ],
        ]);

        $camara = Camara::where('serial', $data['camara'])->first();

        $consulta_anterior = Consulta::where('placa', $data['placa'])
            ->where('camara_id', $camara->id)
            ->whereIn('estado', [
                ConsultaEstado::Procesada->value,
                ConsultaEstado::Rechazada->value,
            ])
            ->whereDate('fecha', '=', date('Y-m-d'))
            ->first();

        if($consulta_anterior){
            return response()->json([
                'estado' =>  $consulta_anterior->estado->descripcion(),
                'consulta' => $consulta_anterior
            ], 200);
        }

        return response()->json([
            'estado' =>  ConsultaEstado::Pendiente->descripcion(),
            'consulta' => Consulta::create([
                'placa' => $data['placa'],
                'camara_id' => $camara->id,
                'fecha' => date('Y-m-d'),
            ])
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Consulta  $consulta
     * @return \Illuminate\Http\Response
     */
    public function show(Consulta $consulta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consulta  $consulta
     * @return \Illuminate\Http\Response
     */
    public function edit(Consulta $consulta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consulta  $consulta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Consulta $consulta)
    {
        if(Carbon::parse($consulta->fecha) < Carbon::today() && $consulta->estado->value == ConsultaEstado::Pendiente->value){
            Consulta::where('placa', $consulta->placa)
                ->whereDate('fecha', '<', date('Y-m-d'))
                ->update(['estado' => ConsultaEstado::Rechazada->value]);
            
            return response()->json([
                'estado' =>  ConsultaEstado::Rechazada->descripcion(),
                'consulta' => $consulta->refresh()
            ], 400);
        }

        if($consulta->estado->value != ConsultaEstado::Pendiente->value){
            return response()->json([
                'estado' =>  $consulta->estado->descripcion(),
                'consulta' => $consulta
            ], 200);
        }

        $data = $request->validate([
            'respuesta_bien' => ['required', 'boolean'],
            'soat_vigente' => ['required_if:respuesta_bien,true', 'nullable', 'boolean'],
            'rtm_vigente' => ['required_if:respuesta_bien,true', 'nullable', 'boolean'],
            'clase' => ['required_if:respuesta_bien,true', 'nullable', 'string'],
            'marca' => ['required_if:respuesta_bien,true', 'nullable', 'string'],
            'servicio' => ['required_if:respuesta_bien,true', 'nullable', 'string'],
            'color' => ['required_if:respuesta_bien,true', 'nullable', 'string'],
            'modelo' => ['required_if:respuesta_bien,true', 'nullable', 'string'],
        ]);

        $query = Consulta::where('placa', $consulta->placa)
            ->whereDate('fecha', '=', date('Y-m-d'));

        if($data['respuesta_bien']){
            unset($data['respuesta_bien']);

            $query->update(array_merge($data, [
                'estado' => ConsultaEstado::Procesada->value,
            ]));
        } else {
            $query->update([
                'estado' => ConsultaEstado::Rechazada->value,
            ]);
        }

        $consulta = $consulta->refresh();

        return response()->json([
            'estado' =>  $consulta->estado->descripcion(),
            'consulta' => $consulta->refresh()
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Consulta  $consulta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Consulta $consulta)
    {
        //
    }
}
