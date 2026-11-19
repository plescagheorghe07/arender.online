<?php

namespace App\Models;

enum StaffRole: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Support = 'support';

    public function managesCatalog(): bool
    {
        return $this === self::Admin || $this === self::Manager;
    }
}
