<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory,
        SoftDeletes;
    
    protected $table = 'notifications';
    
    protected $fillable = [
        'type', 'notifiable', 'data', 'read_at'
    ];
    
    protected $casts = [
        'read_at' => 'datetime',
    ];
    
    protected $attributes = [
        'type' => '',
        'notifiable' => '',
        'data' => '',
        'read_at' => '',
    ];
}
