<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComponentController extends Controller
{
    private $repository;
    
    public function __construct(\App\Repositories\ComponentRepository $repository) {
        $this->repository = $repository;
        
        //$this->middleware('can:component list', ['only' => ['index', 'show']]);
        //$this->middleware('can:component create', ['only' => ['create', 'stoew']]);
        //$this->middleware('can:component edit', ['only' => ['edit', 'update']]);
        //$this->middleware('can:component delete', ['only' => ['destroy']]);
        //$this->middleware('can:component restore', ['only' => ['restore']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        return \Inertia\Inertia::render('Components/ComponentsList');
    }

    public function getComponentsToTable(Request $request){}
    
    public function getComponents(){}
    
    public function getComponentById($id){}
    
    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }
    
    public function _getRoles() {
        return [
            //'list' => Auth::user()->can('component list'),
            //'create' => Auth::user()->can('component create'),
            //'edit' => Auth::user()->can('component edit'),
            //'delete' => Auth::user()->can('component delete'),
            //'restore' => Auth::user()->can('component restore'),
        ];
    }
}
