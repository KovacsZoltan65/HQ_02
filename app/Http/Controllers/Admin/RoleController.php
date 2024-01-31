<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    private $repository;
    
    public function __construct(\App\Interfaces\RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Support\Facades\Request $request)
    {
        return Inertia::render('Roles/RolesList');
    }
    
    public function getRoles(Request $request)
    {
        //
        $config = $request->get('config', []);
        //
        $filters = $request->get('filters', []);
        
        if( count($filters) > 0 ){}
        
        $per_page = count($config) != 0 && isset($config['per_page']) 
            ? $config['per_page'] 
            : config('app.per_page');
        
        $roles = \App\Models\Role::query()->paginate($per_page);
        
        $data = [
            'roles' => $roles,
            'config' => $config,
            'filters' => $filters,
        ];
        
        return response()->json($data, \Illuminate\Http\Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
