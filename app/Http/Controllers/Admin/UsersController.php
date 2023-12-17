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
    
    public function store(){
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
        ]);

        return User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
        ]);
    }
    
    public function update(User $user){
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:8',
        ]);

        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ? bcrypt(request('password')) : $user->password,
        ]);

        return $user;
    }
    
    public function destory(User $user) {
        $user->delete();

        return response()->noContent();
    }
    
    public function getUsers(Request $request) {
        
        $users = User::query()
            ->when(request('query'), function($query, $searchQuery){
                $query->where('name', 'like', "%{$searchQuery}%");
            })
            ->latest()
            ->paginate(config('app.pagination_limit'));

        return $users;
    }
    
    public function changeRole(User $user){
        $user->update([
            'role' => request('role')
        ]);
        
        return response()->json(['wessage' => 'Users deleted successfully!']);
    }
    
    public function bulkDelete(){
        User::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Users deleted successfully!']);
    }
}
