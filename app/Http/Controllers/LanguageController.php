<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        // Frontend felől jövő nyelv
        $locale = $request->get('locale');
        // Új nyelv beállítása
        app()->setLocale($locale);
        // Új nyelv eltárolása a session -be
        session()->put('locale', $locale);
        
        return redirect()->back();
    }
}
