<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoRedSocial extends Model
{
    protected $table = 'barberia.empleado_red_social';

    protected $fillable = ['empleado_id', 'plataforma', 'url', 'orden'];
}
