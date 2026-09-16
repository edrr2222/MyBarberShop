<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoRedSocial extends Model
{
    public const PLATAFORMAS = [
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'tiktok' => 'TikTok',
        'whatsapp' => 'WhatsApp',
        'otro' => 'Otro',
    ];

    protected $table = 'barberia.empleado_red_social';

    protected $fillable = ['empleado_id', 'plataforma', 'url', 'orden'];
}
