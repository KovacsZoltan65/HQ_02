<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Foundation\Auth\User as User2;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class UsersController extends Controller
{
    private $repository;
    
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    public function index()
    {
        
        return Inertia::render('Admin/Users/UsersList');
    }
    
    public function create(Request $request){
        
    }
    
    public function store(StoreUserRequest $request){
        $user = $this->repository->create($request->all());
        
        return redirect()->back()->with('message', 'USER CREATED');
    }
    
    public function edit(User $user)
    {
        //
    }
    
    public function update(Request $request, $id)
    {
        $user = $this->repository->update($request->all(), $id);
        
        return response()->json($user, Response::HTTP_OK);
    }
    
    public function destory(User $user) {
        $this->repository->delete($id);
        
        return redirect()->back()->with('message', 'USER DELETED');
    }
    
    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);
        $res = $user->restore();
        
        return redirect()->back()->with('message', 'USER RESTORE');
    }
    
    public function getUsers(Request $request) {
        
        $users = User2::query()->paginate(config('app.pagination_limit'));

        $data = [
            'users' => $users,
        ];

        return response()->json($data, Response::HTTP_OK);
    }
    
    public function changeRole(User $user){
        $user->update([
            'role' => request('role')
        ]);
        
        return response()->json(['wessage' => 'Users deleted successfully!']);
    }
    
    public function bulkDelete(){
        User2::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Users deleted successfully!']);
    }
}
