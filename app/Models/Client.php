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
        // No filtrar por barberia_id aquí: en eager loading (::with()) Eloquent
        // construye esta relación con una instancia vacía del modelo, así que
        // $this->barberia_id sería null y rompería el where. No hace falta de
        // todas formas: client_id ya acota la tarjeta a un único cliente/barbería.
        return $this->hasOne(LoyaltyCard::class, 'client_id')
            ->whereIn('estado', ['activa', 'completada'])
            ->latestOfMany();
    }
}
