<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    protected $table = 'tenant.sede';

    protected $fillable = ['barberia_id', 'nombre', 'slug', 'direccion', 'telefono', 'estado'];

    protected $casts = ['estado' => 'boolean'];

    public function barberia()
    {
        return $this->belongsTo(Barberia::class, 'barberia_id');
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'sede_id');
    }
}
