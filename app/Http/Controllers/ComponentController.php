<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateComponentRequest;
use App\Http\Requests\UpdateComponentRequest;
use App\Http\Resources\ComponentResource;
use App\Models\Component;
use App\Repositories\ComponentRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class ComponentController extends Controller
{
    private $repository;
    
    /**
     * A description of the entire PHP function.
     *
     * @param ComponentRepository $repository description
     */
    public function __construct(ComponentRepository $repository) {
        $this->repository = $repository;
        
        //$this->middleware('can:component list', ['only' => ['index', 'show']]);
        //$this->middleware('can:component create', ['only' => ['create', 'stoew']]);
        //$this->middleware('can:component edit', ['only' => ['edit', 'update']]);
        //$this->middleware('can:component delete', ['only' => ['destroy']]);
        //$this->middleware('can:component restore', ['only' => ['restore']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param Request $request The request instance
     *
     * @return \Inertia\Response The Inertia response
     */
    public function index(Request $request) {
        // Render the components list page
        return Inertia::render('Components/ComponentsList', [
            'can' => $this->_getRoles()
        ]);
    }

    /**
     * Az összetevők listáját adja vissza az adott szűrő és lapozási beállítások szerint.
     * 
     * @param Request $request The request instance
     * 
     * @return \Illuminate\Http\JsonResponse The JSON response
     */
    public function getComponentsToTable(Request $request) {
        /**
         * A konfigurációs paraméterek
         * @var array
         */
        $config = $request->input('config', []);
        
        /**
         * A szűrő paraméterei
         * @var array
         */
        $filters = $request->input('filters', []);
        
        /**
         * Az aktuális oldalszám
         * @var int
         */
        $page = $request->input('page', 1);
        
        if( count($filters) > 0 ) {
            /**
             * A keresési érték
             * @var string|null
             */
            $value = $filters['search'] ?? null;
            
            if( isset($value) ) {
                $this->repository->findWhere([
                    ['name', 'LIKE', "%$value%"]
                ]);
            }
            
            // ------------------
            // RENDEZÉS
            // ------------------
            /**
             * The column to order by
             * @var string
             */
            $column = $filters['column'] ?? 'type';
            
            /**
             * A rendezés iránya
             * @var string
             */
            $direction = $filters['direction'] ?? 'asc';
            
            $this->repository->orderBy($column, $direction);
        }
        
        /**
         * Az összetevők listája
         * @var \Illuminate\Pagination\LengthAwarePaginator
         */
        $components = $this->repository->paginate( $config['per_page'] ?? $config('app.per_page') );
        
        /**
         * A válaszadatok
         * @var array
         */
        $data = [
            'data' => $components,
            'config' => $config,
            'filters' => $filters,
        ];
        
        return response()->json($data, Response::HTTP_OK);
    }

    public function getComponents() {
        ComponentResource::collection(
                Component::latest()->get()
        );
    }
    
    public function getComponentsToSelect() {
        return ComponentResource::collection(
            Component::select('id', 'name')->orderBy('name', 'asc')->get()
        );
    }
    
    /**
     * Returns a component by its ID
     * 
     * @param int $id The component ID
     * 
     * @return JsonResponse
     */
    public function getComponentById($id) {
        try {
            /** @var Component $component */
            $component = $this->repository->find($id);
            
            if( !$component ) {
                return response()->json([
                    'message' => __('subdomain_not_found'),
                ], Response::HTTP_NOT_FOUND);
            }
            
            return response()->json($component, Response::HTTP_OK);
        } catch( \Exception $e ) {
            return response()->json([
                'message' => __('unexpected_error'),
                'error' => $e->getMessage(),
            ]);
        }
    }

    
    /**
     * Shows the component creation page
     *
     * @param Request $request The request instance
     *
     * @return \Inertia\Response The Inertia response
     */
    public function create(Request $request) {
        $component = new Component();
        
        return Inertia::render('Components/ComponentsCreate', [
            'can' => $this->_getRoles(), // The user's roles
            'data' => $component,       // The component data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateComponentRequest $request) {
        $component = $this->repository->create($request->all());
        
        return redirect()->json('message', __('components_create'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Component $component) {}

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateComponentRequest $request The update request
     * @param int $id The component ID
     *
     * @return JsonResponse The JSON response
     *
     * @throws \Exception
     */
    public function update(UpdateComponentRequest $request, $id) {
        try {
            $component = $this->repository->update($request->all(), $id);
            
            if( !$component ){
                return response()->json([
                    'message' => __('components_not_updated'),
                ], Response::HTTP_NOT_FOUND);
            }
            
            return response()->json([
                'message' => __('components_updated'),
                'subdomain' => $component,
            ], Response::HTTP_OK);
            
        } catch(\Exception $exception) {
            return response()->json([
                'message' => __('components_updated'),
                'error' => $exception->getMessage(),
            ], Response::HTTP_SERVER_ERROR);
        }
    }

    /**
     * Soft-delete the specified resource from storage.
     *
     * @param Component $component The component to delete
     *
     * @return JsonResponse The JSON response
     *
     * @throws \Exception If the component could not be deleted
     */
    public function destroy(Component $component) {
        try {
            $this->repository->delete($component->id);
            
            return response()->json([
                'success' => true,
                'message' => __('components_deleted'),
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => __('unexpected_error'),
                'error' => $exception->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function bulkDelete(Request $request) {
        // Retrieve 'ids' from the request, defaulting to an empty array if not provided
        $ids = $request->get('ids', []);
        
        // If no IDs are provided, redirect back with an error message
        if(empty($ids)) {
            return redirect()->back()->with('error', __('no_components_selected'));
        }

        try {
            // Delete the subdomains with the provided IDs
            Component::destroy($ids);

            // Redirect back with a success message if deletion is successful
            return redirect()->back()->with('message', __('components_bulk_deleted'));
        } catch (\Exception $exception) {
            // If an exception occurs, redirect back with an error message
            return redirect()->back()->with('error', __('unexpected_error') . $exception->getMessage());
        }
    }
    
    public function restore($id) {
        $restored = Component::onlyTrashed()->where('id', $id)->restore();

        if ($restored) {
            $message = __('components_restored');
        } else {
            $message = __('components_not_found_or_not_deleted');
        }
        
        return redirect()->back()->with('message', $message);
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
