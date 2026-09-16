<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'barberia.servicio';

    protected $fillable = [
        'barberia_id', 'nombre', 'descripcion', 'precio',
        'duracion_minutos', 'imagen_url', 'aplica_sello', 'estado', 'orden',
    ];

    protected $casts = ['aplica_sello' => 'boolean', 'estado' => 'boolean'];
}
