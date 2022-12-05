<?php

namespace App\Enums;

/**
 * Listado estados de una consulta
 */
enum ConsultaEstado: int
{
    case Pendiente = 1;
    case Procesada = 2;
    case Rechazada = 3;

    public function descripcion(): string
    {
        return match ($this) {
            ConsultaEstado::Pendiente => 'Pendiente',
            ConsultaEstado::Procesada => 'Procesada',
            ConsultaEstado::Rechazada => 'Rechazada',
        };
    }
}
