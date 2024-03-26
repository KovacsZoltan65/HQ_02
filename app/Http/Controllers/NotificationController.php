<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class NotificationController extends Controller
{
    private $repository;
    
    public function __construct(NotificationRepositoryInterface $repository) {
        $this->repository = $repository;
        
        //$this->middleware('can:notification list', ['only' => ['index', 'show']]);
        //$this->middleware('can:notification create', ['only' => ['create', 'stoew']]);
        //$this->middleware('can:notification edit', ['only' => ['edit', 'update']]);
        //$this->middleware('can:notification delete', ['only' => ['destroy']]);
        //$this->middleware('can:notification restore', ['only' => ['restore']]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        return Inertia::render('Notifications/NotificationList', [
            'can' => $this->_getRoles(),
        ]);
    }

    public function getNotificationsToTable(Request $request) {
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
                    ['type', 'LIKE', "%$value%"],
                    ['notifiable', 'LIKE', "%$value%"],
                    ['data', 'LIKE', "%$value%"],
                    ['read_at', 'LIKE', "%$value%"]
                ]);
            }
            
            // ------------------
            // RENDEZÉS
            // ------------------
            $column = $filters['column'] ?? 'type';
            $direction = $filters['direction'] ?? 'asc';
            $this->repository->orderBy($column, $direction);
        }
        
        $per_page = $config['per_page'] ?? config('app.per_page');
        
        $notifications = $this->repository->paginate($per_page);
        
        $data = [
            'data' => $notifications,
            'config' => $config,
            'filters' => $filters,
        ];
        
        return response()->json($data, Response::HTTP_OK);
    }
    
    public function getNotifications() {
        return NotificationResource::collection(
            Notification::latest()->get()
        );
    }
    
    public function getNotificationsToSelect() {
        return NotificationResource::collection(
            Notification::select('id', 'type')->orderBy('type', 'asc')->get()
        );
    }
    
    public function getNotificationById($id) {
        try {
            $notification = $this->repository->find($id);
            
            if( !$notification ) {
                return response()->json(['message' => __('notification_not_found')], Response::HTTP_NOT_FOUND);
            }
            
            return response()->json($notification, Response::HTTP_OK);
        } catch( \Exception $exception ) {
            return response()->json([
                'message' => __('unexpected_error'), 
                'error' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        //return Notification::findOrFail($id);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {
        $notification = new Notification();
        
        return Inertia::render('Notifications/NotificationsList', [
            'data' => $notification,
            'can' => $this->_getRoles(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotificationRequest $request) {
        $notification = $this->repository->create($request->all());
        
        return redirect()->json('message', __('notifications_create'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationRequest $request, $id) {
        try {
            $notification = $this->repository->update($request->all(), $id);
            
            if( !$notification ) {
                return response()->json([
                    'message' => __('notifications_not_updated')
                ]);
            }
            
            return response()->json([
                'message' => __('notifications_updated'),
                'data' => $notification,
            ], Response::HTTP_OK);
            
        } catch( \Exception $e ) {
            return response()->json([
                'message' => __('notifications_updated'),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification) {
        try {
            
            $this->repository->delete($notification->id);
            
        } catch( \Exception $exception ) {
            return response()->json([
                'success' => false,
                'message' => __('unexpected_error'),
                'error' => $exception->getMessage()
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
            Notification::destroy($ids);

            // Redirect back with a success message if deletion is successful
            return redirect()->back()
                ->with('message', __('notifications_bulk_deleted'));
        } catch (\Exception $exception) {
            // If an exception occurs, redirect back with an error message
            return redirect()->back()
                ->with('error', __('unexpected_error') . $exception->getMessage());
        }
    }
    
    public function restore($id) {
        $restored = Component::onlyTrashed()->where('id', $id)->restore();

        if ($restored) {
            $message = __('notifications_restored');
        } else {
            $message = __('notifications_not_found_or_not_deleted');
        }
        
        return redirect()->back()->with('message', $message);
    }
    
    public function _getRoles() {
        return [
            //'list' => Auth::user()->can('notification list'),
            //'create' => Auth::user()->can('notification create'),
            //'edit' => Auth::user()->can('notification edit'),
            //'delete' => Auth::user()->can('notification delete'),
        ];
    }
}
