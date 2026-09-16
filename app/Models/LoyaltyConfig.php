<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyConfig extends Model
{
    protected $table = 'loyalty.config';

    protected $fillable = ['barberia_id', 'sellos_requeridos', 'qr_token_segundos', 'activo'];

    protected $casts = ['activo' => 'boolean'];
}
