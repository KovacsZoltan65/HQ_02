<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subdomain extends Model
{
    use HasFactory, 
        SoftDeletes;

    protected $table = 'subdomains';
    protected $casts = [
                      'db_port' => 'int',
                 'notification' => 'bool',
                     'state_id' => 'int',
                    'is_mirror' => 'bool',
                          'sso' => 'bool',
        'access_control_system' => 'int',
                  'last_export' => 'datetime',
    ];
    
    protected $attributes = [
             'db_port' => 3306,
             'db_host' => 'localhost',
        'notification' => 1,
            'state_id' => 1,
           'is_mirror' => 0,
                 'sso' => 0,
        'access_control_system' => 0,
    ];

    protected $fillable = [
		'subdomain', 'url', 'name',
		'db_host', 'db_port', 'db_name', 'db_user', 'db_password',
		'notification', 'state_id', 'is_mirror', 'sso',
		'access_control_system', 'last_export'
	];
}
