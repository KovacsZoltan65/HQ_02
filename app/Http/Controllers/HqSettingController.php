<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateHqSettingRequest;
use App\Interfaces\HqSettingRepositoryInterface;
use App\Models\HqSetting;
use Illuminate\Http\Request;

class HqSettingController extends Controller
{
    private $repository;
    
    public function __construct(HqSettingRepositoryInterface $repository) {
        $this->repository = $repository;
        
        //$this->middleware('can:hq_setting list', ['only' => ['index', 'show']]);
        //$this->middleware('can:hq_setting create', ['only' => ['create', 'stoew']]);
        //$this->middleware('can:hq_setting edit', ['only' => ['edit', 'update']]);
        //$this->middleware('can:hq_setting delete', ['only' => ['destroy']]);
        //$this->middleware('can:hq_setting restore', ['only' => ['restore']]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        return \Inertia\Inertia::render('HqSettings/HqSettingsList', [
            'can' => $this->_getRoles(),
        ]);
    }

    public function getHqSettingsToTable(Request $request) {
        
    }
    
    public function getHqSettings() {
        return \App\Http\Resources\HqSettingResource::collection(
            HqSetting::latest()->get()
        );
    }
    
    public function getHqSettingsToSelect() {
        return \App\Http\Resources\HqSettingResource::collection(
            HqSetting::select('key', 'value')->orderBy('key', 'asc')->get()
        );
    }
    
    public function getHqSettingById($id) {}
    
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreHqSettingRequest $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HqSetting $hq_setting) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHqSettingRequest $request, string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }
    
    public function bulkDelete(Request $request) {}
    
    public function restore($id) {}
    
    public function _getRoles() {
        return [
            //'list' => Auth::user()->can('hq_setting list'),
            //'create' => Auth::user()->can('hq_setting create'),
            //'edit' => Auth::user()->can('hq_setting edit'),
            //'delete' => Auth::user()->can('hq_setting delete'),
        ];
    }
}
