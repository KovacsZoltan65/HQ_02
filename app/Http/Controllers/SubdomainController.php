<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubdomainRequest;
use App\Http\Requests\UpdateSubdomainRequest;
use App\Interfaces\SubdomainRepositoryInterface;
use App\Models\Subdomain;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class SubdomainController extends Controller
{
    private $repository;
    
    public function __construct(SubdomainRepositoryInterface $repository) {
        $this->repository = $repository;
        
        //$this->middleware('can:subdomain list', ['only' => ['index', 'show']]);
        //$this->middleware('can:subdomain create', ['only' => ['create', 'stoew']]);
        //$this->middleware('can:subdomain edit', ['only' => ['edit', 'update']]);
        //$this->middleware('can:subdomain delete', ['only' => ['destroy']]);
        //$this->middleware('can:subdomain restore', ['only' => ['restore']]);
    }
    
    public function index(Request $request) {
        return Inertia::render('Subdomains/SubdomainsList');
    }
    
    public function getSubdomainsToTable(Request $request) {
        //
        $config = $request->input('config', []);
        //
        $filters = $request->input('filters', []);
        //
        $page = $request->input('page', 1);
        
        if( isset($filters['search']) ) {
            $value = $filters['search'];
            $this->repository->findWhere([
                ['name','like', "%$value%"],
                ['url', 'like', "%$value%"],
                ['db_name', 'like', "%$value%"],
                ['db_user', 'like', "%$value%"],
            ]);
        }
        
        $column = $filters['column'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';
        
        $subdomains = $this->repository
                ->orderBy($column, $direction)
                ->paginate( $config['per_page'] ?? config('app.per_page') );
        
        $data = [
            'data' => $subdomains,
            'config' => $config,
            'filters' => $filters,
        ];
        
        return response()->json($data, Response::HTTP_OK);
    }
    
    public function getSubdomains() {
        $subdomains = Subdomain::all();
        
        return response()->json($subdomains, Response::HTTP_OK);
    }
    
    /**
     * Get a subdomain by its ID.
     *
     * @param mixed $id The identifier of the subdomain to retrieve.
     * @throws \Exception When there is a database or application error.
     * @return \Illuminate\Http\JsonResponse Returns subdomain data if found, or an error message.
     */
    public function getSubdomainById($id) {
        try {
            // Retrieve the subdomain using the repository.
            $subdomain = $this->repository->find($id);

            // Check if the subdomain was found.
            if (!$subdomain) {
                // Respond with a not found status if no subdomain is found.
                return response()->json(['message' => __('subdomain_not_found')], Response::HTTP_NOT_FOUND);
            }

            // Respond with the subdomain data and an OK status.
            return response()->json($subdomain, Response::HTTP_OK);
        } catch (\Exception $exception) {
            // Respond with an error message and a server error status if an exception occurs.
            return response()->json([
                'message' => __('unexpected_error'), 
                'error' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function getSubdomainsToSelect() {
        return \App\Http\Resources\SubdomainResource::collection(Subdomain::all());
    }
    
    public function create(Request $request) {
        $subdomain = new Subdomain();
        
        return Inertia::render('Subdomains/SubdomainsCreate', [
            'can' => $this->_getRoles(),
            'subdomain' => $subdomain,
        ]);
    }
    
    public function store(StoreSubdomainRequest $request) {
        $subdomain = $this->repository->create($request->all());
        
        return redirect()->back()->with('message', __('subdomains_created'));
    }
    
    public function show(string $id) {}
    
    public function edit(Subdomain $subdomain) {}
    
    /**
     * Update an existing subdomain.
     *
     * @param UpdateSubdomainRequest $request The request object with validated data
     * @param int|string $id The identifier of the subdomain to update
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSubdomainRequest $request, $id) {
        try {
            // Attempt to update the subdomain with the validated request data
            $subdomain = $this->repository->update($request->validated(), $id);

            if (!$subdomain) {
                // If the subdomain wasn't updated, return a not found response
                return response()->json(['message' => __('subdomain_not_updated')], Response::HTTP_NOT_FOUND);
            }

            // If the subdomain was updated successfully, return the updated subdomain data
            return response()->json([
                'message' => __('subdomain_updated'),
                'subdomain' => $subdomain
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            // In case of any exception, return a server error response
            return response()->json([
                'message' => __('unexpected_error'),
                'error' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Delete a given subdomain.
     *
     * @param Subdomain $subdomain The subdomain instance to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Subdomain $subdomain) {
        try {
            // Delete the subdomain by its ID using the repository
            $this->repository->delete($subdomain->id);

            // Return a JSON response indicating success
            return response()->json([
                'success' => true,
                'message' => __('subdomain_deleted')
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            // Return a JSON response indicating an error with exception message
            return response()->json([
                'success' => false,
                'message' => __('unexpected_error'),
                'error' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Delete multiple subdomains in bulk based on provided IDs.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkDelete(Request $request) {
        // Retrieve 'ids' from the request, defaulting to an empty array if not provided
        $ids = $request->get('ids', []);
        
        // If no IDs are provided, redirect back with an error message
        if(empty($ids)) {
            return redirect()->back()->with('error', __('no_subdomains_selected'));
        }

        try {
            // Delete the subdomains with the provided IDs
            Subdomain::destroy($ids);

            // Redirect back with a success message if deletion is successful
            return redirect()->back()->with('message', __('subdomains_bulk_deleted'));
        } catch (\Exception $exception) {
            // If an exception occurs, redirect back with an error message
            return redirect()->back()->with('error', __('unexpected_error') . $exception->getMessage());
        }
    }
    
    /**
     * Restore a soft deleted subdomain by its ID.
     *
     * @param mixed $id The ID of the subdomain to restore.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id) {
        $restored = Subdomain::onlyTrashed()->where('id', $id)->restore();

        if ($restored) {
            $message = __('subdomains_restored');
        } else {
            $message = __('subdomain_not_found_or_not_deleted');
        }
        
        return redirect()->back()->with('message', $message);
    }
    
    public function _getRoles() {
        return [
            //'list' => Auth::user()->can('subdomain list'),
            //'create' => Auth::user()->can('subdomain create'),
            //'edit' => Auth::user()->can('subdomain edit'),
            //'delete' => Auth::user()->can('subdomain delete'),
        ];
    }
}