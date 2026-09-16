<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Empleado extends Authenticatable
{
    protected $table = 'barberia.empleado';

    protected $fillable = ['sede_id', 'nombre', 'usuario', 'password', 'foto_url', 'descripcion', 'estado'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['estado' => 'boolean'];

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function redesSociales()
    {
        return $this->hasMany(EmpleadoRedSocial::class, 'empleado_id')->orderBy('orden');
    }
}
