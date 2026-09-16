<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Admin;

trait ScopedAdmin
{
    protected function admin(): Admin
    {
        return auth('admin')->user();
    }

    protected function barberiaId(): int
    {
        return $this->admin()->barberia_id;
    }

    protected function esAdminDeBarberiaCompleta(): bool
    {
        return $this->admin()->esAdminDeBarberiaCompleta();
    }
}
