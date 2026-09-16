<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarberiaRedSocial extends Model
{
    public const PLATAFORMAS = [
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'tiktok' => 'TikTok',
        'whatsapp' => 'WhatsApp',
        'web' => 'Sitio web',
        'otro' => 'Otro',
    ];

    protected $table = 'tenant.barberia_red_social';

    protected $fillable = ['barberia_id', 'plataforma', 'url', 'orden'];
}
