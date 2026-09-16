<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'tenant.admin';

    protected $fillable = ['barberia_id', 'sede_id', 'nombre', 'usuario', 'password', 'rol', 'estado'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['estado' => 'boolean'];

    public function esAdminDeBarberiaCompleta(): bool
    {
        return is_null($this->sede_id);
    }
}
