<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubdomainState extends Model
{
    use HasFactory, 
        SoftDeletes;
    
    protected $table = 'subdomain_states';
    
    protected $fillable = ['name'];
    
    protected $attributes = [
        'name' => 'state 01',
    ];
}
