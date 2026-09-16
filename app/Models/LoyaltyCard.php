<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyCard extends Model
{
    protected $table = 'loyalty.card';

    protected $fillable = ['client_id', 'barberia_id', 'sellos_actuales', 'estado', 'completed_at'];

    public function stamps()
    {
        return $this->hasMany(LoyaltyStamp::class, 'card_id');
    }
}
