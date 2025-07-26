<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Get admin role.
     */
    public static function admin(): Role
    {
        return Role::where(['name' => 'admin'])->firstOrFail();
    }

    /**
     * Get consumer role.
     */
    public static function consumer(): Role
    {
        return Role::where(['name' => 'consumer'])->firstOrFail();
    }
}
