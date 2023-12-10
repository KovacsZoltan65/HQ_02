<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index(){
        
        return Inertia::render('Admin/Users/UsersList');
    }
    
    public function getUsers(Request $request) {
        
        // Beállítások
        $config = $request->get('config', []);
        // Szűrők és keresések
        $filters = $request->get('filters', []);
        
        $users = User::all();
        //dd($users);
        $data = [
            'users' => $users,
            'config' => $config,
            'filters' => $filters,
            
        ];
        
        return response()->json($data, Response::HTTP_OK);
    }
}
