<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HqSetting extends Model
{
    use HasFactory, 
        SoftDeletes;
    
    protected $table = 'hq_settings';
    
    protected $fillable = [
        'key',
        'value'
    ];
}
