<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class RoleController extends Controller
{
    private $repository;
    
    public function __construct(RoleRepositoryInterface $repository) {
        $this->repository = $repository;
        
        //$this->middleware('can:role list', ['only' => ['index', 'show']]);
        //$this->middleware('can:role create', ['only' => ['create', 'store']]);
        //$this->middleware('can:role edit', ['only' => ['edit', 'update']]);
        //$this->middleware('can:role delete', ['only' => ['destroy', 'bulkDelete']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request) {
        return Inertia::render('Roles/RolesList', [
            'can' => $this->_getRoles(),
        ]);
    }
    
    public function getRolesToTable(Request $request) {
        // Szerezze be a konfigurációt és a szűrőket a kérésből
        $config = $request->input('config', []);
        $filters = $request->input('filters', []);
        // Oldal
        $page = $request->input('page', 1);
        
        // Engedélyek keresése, ha van keresési szűrő
        if( isset($filters['search']) )
        {
            $value = $filters['search'];
            $this->repository->findWhere([
                ['name','like', "%$value%"],
                ['guard_name', 'like', "%$value%"],
            ]);
        }
        
        // Állítsa be a rendelés oszlopát és irányát
        $column = $filters['column'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';
        
        // Rendelés alkalmazása a lekérdezésre
        //$this->repository->orderBy($column, $direction);
        
        // Állítsa be az oldalankénti engedélyek számát
        //$per_page = $config['per_page'] ?? config('app.per_page');
        // Szerezze be az engedélyeket oldalszámozással
        //$roles = Role::query()->paginate($per_page);
        $roles = $this->repository
            ->orderBy($column, $direction)
            ->with('permissions')
            ->paginate( $config['per_page'] ?? config('app.per_page') );
        //dd($roles);
        // Készítse elő a visszaküldendő adatokat
        $data = [
            'data' => $roles,
            'config' => $config,
            'filters' => $filters,
        ];
        
        // Adja vissza az adatokat JSON-válaszként
        return response()->json($data, Response::HTTP_OK);
    }
    
    public function getRoles() {
        $roles = Role::all();

        return response()->json($roles, Response::HTTP_OK);
    }
    
    public function getRoleById($id) {
        $role = Role::find($id);

        return response()->json($role, Response::HTTP_OK);
    }
    
    public function getRolesToSelect() {
        return RoleResource::collection(
            Role::orderBy('name', 'asc')->get()
        );
    }
    
    public function create(Request $request) {
        $role = new Role();
        
        return Inertia::render('Roles/RolesCreate', [
            'can' => $this->_getRoles(),
            'role' => $role,
        ]);
    }
    
    public function store(StoreRoleRequest $request) {

        $role = $this->repository->create($request->all());
        
        return redirect()->back()->with('message', __('roles_created'));
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id) {}
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role) {}
    
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, $id) {
        $role = $this->repository->update($request->all(), $id);
        
        return response()->json($role, Response::HTTP_OK);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role) {
        
        $this->repository->delete($role->id);
        //return redirect()->back()->with('message', __('roles_deleted'));
        return response()->json([
            'success' => true,
            'message' => __('roles_deleted'),
        ], 200);
    }
    
    
    public function bulkDelete() {
        Role::whereIn('id', request('ids'))->delete();

        return redirect()->back()->with('message', __('roles_bulk_updated'));
    }
    
    
    
    public function _getRoles() {
        return [
            //'list' => Auth::user()->can('role list'),
            //'create' => Auth::user()->can('role create'),
            //'edit' => Auth::user()->can('role edit'),
            //'delete' => Auth::user()->can('role delete'),
        ];
    }
}
