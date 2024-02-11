<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class RoleController extends Controller
{
    private $repository;
    
    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
        
        //$this->middleware('can:role list', ['only' => ['index', 'show']]);
        //$this->middleware('can:role create', ['only' => ['create', 'store']]);
        //$this->middleware('can:role edit', ['only' => ['edit', 'update']]);
        //$this->middleware('can:role delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //dd($permissions);
        return Inertia::render('Roles/RolesList', [
            'can' => $this->_getRoles(),
        ]);
    }
    
    public function getRoles(Request $request)
    {
        //
        $config = $request->get('config', []);
        //
        $filters = $request->get('filters', []);
        
        if( count($filters) > 0 )
        {
            if( isset($filters['search']) )
            {
                $value = $filters['search'];
                $this->repository->findWhere([
                    ['name','like', "%$value%"],
                    ['guard_name', 'like', "%$value%"],
                ]);
            }
            
            $column = (isset($filters['column'])) ? $filters['column'] : 'name';
            $direction = (isset($filters['direction'])) ? '' : 'asc';
            
            $this->repository->orderBy($column, $direction);
        }
        
        $per_page = count($config) != 0 && isset($config['per_page']) 
            ? $config['per_page'] 
            : config('app.per_page');
        
        $roles = Role::query()->paginate($per_page);
        
        foreach( $roles as $role ){
            $rolePermissions = \App\Models\Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
                ->where("role_has_permissions.role_id", $role->id)
                ->select('id', 'name')
                ->get()->toArray();
            $role->permissions = $rolePermissions;
        }
        
        $data = [
            'roles' => $roles,
            'config' => $config,
            'filters' => $filters,
        ];
        \Log::info( print_r($data, true) );
        return response()->json($data, Response::HTTP_OK);
    }

    public function getRolesToSelect(){
        return \App\Http\Resources\RoleResource::collection(Role::all());
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $role = new Role();
        
        return Inertia::render('Roles/RolesCreate', [
            'can' => $this->_getRoles(),
            'role' => $role,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        //dd('RoleController@store $request', $request->all());
        $role = $this->repository->create($request->all());
        
        return redirect()->back()->with('message', __('roles_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        //dd('RoleController@update $request', $request->all(), $id);
        $role = $this->repository->update($request->all(), $id);
        
        return response()->json($role, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //dd('RoleController@destroy', $role);
        $this->repository->delete($role->id);
        
        return redirect()->back()->with('message', __('roles_deleted'));
    }
    
    public function bulkDelete()
    {
        //dd('RoleController@bulkDelete', $request->all());
        Role::whereIn('id', request('ids'))->delete();

        return redirect()->back()->with('message', __('roles_bulk_updated'));
    }
    
    public function restore($id)
    {
        //dd('RoleController@restore', $id);
        $role = Role::onlyTrashed()->find($id);
        $role->restore();
        
        return redirect()->back()->with('message', __('roles_restored'));
    }
    
    public function _getRoles(){
        return [
            //'list' => Auth::user()->can('role list'),
            //'create' => Auth::user()->can('role create'),
            //'edit' => Auth::user()->can('role edit'),
            //'delete' => Auth::user()->can('role delete'),
        ];
    }
}
