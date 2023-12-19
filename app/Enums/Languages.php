<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum Languages: string
{
    use HasOptions;
    
    case HU = 'hu';
    case EN = 'en';

    public function displayName(): string
    {
        return match($this)
        {
            Languages::HU => 'Hungarian',
            Languages::EN => 'English',
        };
    }
}