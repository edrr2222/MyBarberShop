<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyStamp extends Model
{
    protected $table = 'loyalty.stamp';

    protected $fillable = ['card_id', 'empleado_id', 'sede_id', 'servicio_id'];
}
