<?php

namespace App\Models;

use App\Enums\ConsultaEstado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consulta extends Model
{
    use HasFactory;

    protected $table = 'consultas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'placa',
        'camara_id',
        'fecha',
        'soat_vigente',
        'rtm_vigente',
        'clase',
        'marca',
        'servicio',
        'color',
        'modelo',
        'estado',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'soat_vigente' => 'boolean',
        'rtm_vigente' => 'boolean',
        'fecha' => 'date:Y-m-d',
        'estado' => ConsultaEstado::class,
    ];

    public function camara(): BelongsTo
    {
        return $this->belongsTo(Camara::class, 'camara_id');
    }

    public static function regexPlacasVehiculares(): string
    {
        $cases = [
            '[a-zA-Z]{1}[0-9]{4}',
            '[a-zA-Z]{3}[0-9]{3}',
            '[a-zA-Z]{2}[0-9]{4}',
            '[a-zA-Z]{1}[0-9]{5}',
            '[a-zA-Z]{2}[0-9]{3}',
            '[a-zA-Z]{3}[0-9]{2}',
            '[a-zA-Z]{3}[0-9]{2}[a-zA-Z]{1}',
            '[0-9]{3}[a-zA-Z]{2}',
            '[0-9]{3}[a-zA-Z]{3}',
            '[a-zA-Z]{2}[0-9]{5}',
            '[a-zA-Z]{3}[0-9]{4}',
        ];

        return '/^('.implode('|', $cases).')$/';
    }
}
