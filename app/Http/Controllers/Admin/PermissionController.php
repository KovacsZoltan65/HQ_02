<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Interfaces\PermissionRepositoryInterface;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class PermissionController extends Controller
{
    private $repository;
    
    public function __construct(PermissionRepositoryInterface $repository) {
        $this->repository = $repository;
        
        //$this->middleware('can:permission list', ['only' => ['index', 'show']]);
        //$this->middleware('can:permission create', ['only' => ['create', 'store']]);
        //$this->middleware('can:permission edit', ['only' => ['edit', 'update']]);
        //$this->middleware('can:permission delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Permissions/PermissionsList', [
            'can' => $this->_getRoles(),
        ]);
    }
    
    public function getPermissions(Request $request)
    {
        //
        $config = $request->get('config', []);
\Log::info('$config: ' . print_r($config, true));
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
        
        $permissions = Permission::query()->paginate($per_page);
        
        $data = [
            'permissions' => $permissions,
            'config' => $config,
            'filters' => $filters,
        ];
        
        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $permission = new Permission();
        
        return Inertia::render('Permissions/PermissionsCreate', [
            'can' => $this->_getRoles(),
            'permission' => $permission,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        $permission = $this->repository->create($request->all());
        
        return redirect()->back()->with('message', __('permissions_created'));
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
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, $id)
    {
        $permission = $this->repository->update($request->all(), $id);
        
        return response()->json($role, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $this->repository->delete($permission->id);
        
        return redirect()->back()->with('message', __('permissions_deleted'));
    }
    
    public function bulkDelete()
    {
        Permission::whereIn('id', request('ids'))->delete();

        return redirect()->back()->with('message', __('permissions_bulk_updated'));
    }
    
    public function restore($id)
    {
        $permissions = Permission::onlyTrashed()->find($id);
        $permissions->restore();
        
        return redirect()->back()->with('message', __('permissions_restored'));
    }
    
    public function _getRoles(){
        return [
            //'list' => Auth::user()->can('permission list'),
            //'create' => Auth::user()->can('permission create'),
            //'edit' => Auth::user()->can('permission edit'),
            //'delete' => Auth::user()->can('permission delete'),
        ];
    }
}
