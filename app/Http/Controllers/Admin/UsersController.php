<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
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
        dd( __('users_created_success') );
        return Inertia::render('Admin/Users/UsersList', [
            'languageOptions' => \App\Enums\Languages::options(),
        ]);
    }
    
    public function create(Request $request){
        
    }
    
    public function store(StoreUserRequest $request)
    {
        $user = $this->repository->create($request->all());
        
        return redirect()->back()->with('message', 'USER CREATED');
    }
    
    public function edit(User $user)
    {
        //
    }
    
    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->repository->update($request->all(), $id);
        
        return response()->json($user, Response::HTTP_OK);
    }
    
    public function chengePassword(ChangePasswordRequest $request, $id){
        \Log::info('id: ' . print_r($id, true));
        \Log::info('request all: ' . print_r($request->all(), true));
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
    
    public function getUsers(Request $request)
    {
        //
        $config = $request->get('config', []);
        //
        $filters = $request->get('filter', []);
        //
        if( count($filters) > 0 ){
            //
            if( isset($filters['search']) ){
                //
                $value = $filters['search'];
                //
                $this->repository->findWhere([
                    ['name', 'like', "%$value%"], 
                    ['email', 'like', "%$value%"]
                ]);
            }

            //
            $column = 'name';
            //
            if( isset($filters['column']) ){
                //
                $column = $filters['column'];
            }
            //
            $direction = 'asc';
            //
            if( isset($filters['direction']) ){
                // azt állítom be
                $direction = $filters['direction'];
            }
            //
            $this->repository->orderBy($column, $direction);
        }
        //
        $per_page = count($config) != 0 && isset($config['per_page']) 
            ? $config['per_page'] 
            : config('app.per_page');


        $users = User::query()->paginate($per_page);

        $data = [
            'users' => $users,
            'config' => $config,
            'filters' => $filters,
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
        User::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Users deleted successfully!']);
    }
}
