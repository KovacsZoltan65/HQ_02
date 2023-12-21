<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubdomainRequest;
use App\Http\Requests\UpdateSubdomainRequest;
use App\Models\Subdomain;
use App\Repositories\SubdomainRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubdomainController extends Controller
{
    private $repository;
    
    public function __construct(SubdomainRepository $repository) {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubdomainRequest $request)
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
    public function edit(Subdomain $subdomain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubdomainRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subdomain $subdomain)
    {
        //
    }
    
    public function restore($id)
    {
        //
    }
    
    public function getSubdomains(Request $request)
    {
        //
        $config = $request->get('config', []);
        //
        $filters = $request->get('filter', []);
        
        if( count($filters) > 0 )
        {
            if( isset($filters['search']) )
            {
                $value = $filters['search'];
                $this->repository->findWhere([
                    ['subdomain', 'like', "%$value%"],
                    ['url', 'like', "%$value%"],
                    ['name', 'like', "%$value%"],
                ]);
            }
            
            $column = (isset($filters['column'])) ? $filters['column'] : 'name';
            $direction = (isset($filters['direction'])) ? '' : 'asc';
            
            $this->repository->orderBy($column, $direction);
        }
        
        $per_page = ( isset($config['per_page']) && count($config['per_page']) ) 
            ? $config['per_page'] 
            : config('app.per_page');
        
        $subdomains = Subdomain::query()->paginate($per_page);
        
        $data = [
            'subdomains' => $subdomains,
            'config' => $config,
            'filters' => $filters,
        ];
    }
    
    public function bulkDelete(Request $request)
    {
        Subdomain::whereIn('id', $request->get('ids'))->delete();
        
        return response()->json(['message' => 'Users deleted_successfully!']);
    }
}
