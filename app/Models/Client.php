<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    protected $table = 'tenant.client';

    protected $fillable = ['barberia_id', 'cedula', 'nombre', 'password', 'estado'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['estado' => 'boolean'];

    public function barberia()
    {
        return $this->belongsTo(Barberia::class, 'barberia_id');
    }

    public function tarjetaActiva()
    {
        return $this->hasOne(LoyaltyCard::class, 'client_id')
            ->where('barberia_id', $this->barberia_id)
            ->whereIn('estado', ['activa', 'completada'])
            ->latestOfMany();
    }
}
