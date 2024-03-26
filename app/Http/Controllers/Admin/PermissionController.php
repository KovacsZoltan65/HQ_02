<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request) {
        return Inertia::render('Permissions/PermissionsList', [
            'can' => $this->_getRoles(),
        ]);
    }

    /**
     * Szerezzen engedélyeket a kérés paraméterei alapján
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissionsToTable(Request $request) {
        $config = $request->input('config', []);
        $filters = $request->input('filters', []);
        $page = $request->input('page', 1);

        if( count($filters) > 0 ) {
            // ------------------
            // SZŰRÉS
            // ------------------
            if( isset($filters['search']) ) {
                $value = $filters['search'];
                $this->repository->findWhere([
                    ['name', 'LIKE', "%$value%"]
                ]);
            }
            
            // ------------------
            // RENDEZÉS
            // ------------------
            $column = $filters['column'] ?? 'type';
            $direction = $filters['direction'] ?? 'asc';
            $this->repository->orderBy($column, $direction);
        }
        
        $permissions = $this->repository
                ->paginate( $config['per_page'] ?? $config('app.per_page') );
        
        $data = [
            'data' => $permissions,
            'config' => $config,
            'filters' => $filters,
        ];
        
        return response()->json($data, Response::HTTP_OK);
    }

    public function getPermissions(){
        return PermissionResource::collection(
            Permission::latest()->get()
        );
    }

    public function getPermissionsToSelect() {
        return PermissionResource::collection(
            Permission::select('id', 'name')->orderBy('name', 'asc')->get()
        );
    }
    
    public function getPermissionById($id) {
        //$permission = Permission::find($id);

        //return response()->json($permission, Response::HTTP_OK);
        return Permission::findOrFail($id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {
        $permission = new Permission();
        
        return Inertia::render('Permissions/PermissionsCreate', [
            'can' => $this->_getRoles(),
            'permission' => $permission,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request) {

        $permission = $this->repository->create($request->all());
        
        return redirect()->back()->with('message', __('permissions_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, $id) {
        $permission = $this->repository->update($request->all(), $id);
        
        return response()->json($permission, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission) {
        
        $this->repository->delete($permission->id);
        //return redirect()->back()->with('message', __('permissions_deleted'));
        return response()->json([
            'success' => true,
            'message' => __('permissions_deleted'),
        ], 200);
    }
    
    public function bulkDelete() {
        Permission::whereIn('id', request('ids'))->delete();

        return redirect()->back()->with('message', __('permissions_bulk_updated'));
    }
    
    /**
     * Restore a permission by id
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id) {
        // Restore the permission by id
        Permission::onlyTrashed()->where('id', $id)->restore();
        
        // Redirect back with a success message
        return redirect()->back()->with('message', __('permissions_restored'));
    }
    
    public function _getRoles() {
        return [
            //'list' => Auth::user()->can('permission list'),
            //'create' => Auth::user()->can('permission create'),
            //'edit' => Auth::user()->can('permission edit'),
            //'delete' => Auth::user()->can('permission delete'),
        ];
    }
}
