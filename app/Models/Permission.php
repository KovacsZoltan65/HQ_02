<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as OriginalPermission;

class Permission extends OriginalPermission
{
    use HasFactory;

    protected $table = 'permissions';
    
    protected $fillable = [
        'name',
        'guard_name',
        'created_at',
        'updated_at',
    ];
}
