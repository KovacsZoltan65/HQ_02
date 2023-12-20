<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        $locale = $request->get('locale');
        
        app()->setLocale($locale);
        session()->put('locale', $locale);
        
        return redirect()->back();
    }
}
