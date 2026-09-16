<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barberia extends Model
{
    protected $table = 'tenant.barberia';

    protected $fillable = [
        'nombre', 'slug', 'logo_url',
        'color_primario', 'color_secundario', 'color_terciario', 'estado',
    ];

    protected $casts = ['estado' => 'boolean'];

    public function sedes()
    {
        return $this->hasMany(Sede::class, 'barberia_id');
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'barberia_id');
    }

    public function config()
    {
        return $this->hasOne(LoyaltyConfig::class, 'barberia_id');
    }
}
