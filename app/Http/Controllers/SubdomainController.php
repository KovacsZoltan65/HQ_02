<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubdomainRequest;
use App\Http\Requests\UpdateSubdomainRequest;
use App\Models\Subdomain;
use App\Repositories\SubdomainRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

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
        return Inertia::render('Subdomains/SubdomainsList');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request){}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubdomainRequest $request)
    {
        \Log::info('$request: ' . print_r($request->all(), true));
        $subdomain = $this->repository->create($request->all());
        \Log::info('$subdomain: ' . print_r($subdomain->all(), true));
        
        return redirect()->back()->with('message', __('subdomains_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id){}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subdomain $subdomain){}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubdomainRequest $request, $id)
    {
        $subdomain = $this->repository->update($request->all(), $id);
        
        return response()->json($subdomain, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subdomain $subdomain)
    {
        $subdomain = $this->repository->delete($subdomain->id);
        
        return response()->json($subdomain, Response::HTTP_OK);
    }
    
    public function restore($id)
    {
        $subdomain = Subdomain::onlyTrashed()->find($id);
        
        return redirect()->back()->with('message', __('subdomains_restore'));
    }
    
    public function getSubdomains(Request $request)
    {
        //dd($request->get('config', []), $request->get('filters', []));
        //
        $config = $request->get('config', []);
        //
        $filters = $request->get('filters', []);
        //dd($config, $filters);
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
        
        $per_page = count($config) != 0 && isset($config['per_page']) 
            ? $config['per_page'] 
            : config('app.per_page');
        
        $subdomains = Subdomain::query()->paginate($per_page);
        
        $data = [
            'subdomains' => $subdomains,
            'config' => $config,
            'filters' => $filters,
        ];
        
        return response()->json($data, Response::HTTP_OK);
    }
    
    public function getSubdomains2(Request $request){
        //
        $config = $request->get('config', []);
        //
        $filters = $request->get('filters', []);
        
        $subdomains = Subdomain::all();
        
        $data = [
            'subdomains' => $subdomains,
            'config' => $config,
            'filters' => $filters,
        ];
        
        return response()->json($data, Response::HTTP_OK);
    }
    
    public function bulkDelete(Request $request)
    {
        Subdomain::whereIn('id', $request->get('ids'))->delete();
        
        return response()->json(['message' => 'Users deleted_successfully!']);
    }
}
