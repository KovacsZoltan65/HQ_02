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
            'notifications' => $notifications,
            'config' => $config,
            'filters' => $filters,
        ];
        
        return response()->json($data, Response::HTTP_OK);
    }
    
    public function getNotifications() {
        return NotificationResource::collection(Notification::latest()->get());
    }

    
    public function getNotificationById($id) {
        return Notification::findOrFail($id);
    }
    
    public function getNotificationsToSelect() {
        return NotificationResource::collection(
            Notification::select('id', 'type')->orderBy('type', 'asc')->get(['id', 'type'])
        );
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
    public function store(StoreNotificationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
    
    public function bulkDelete(Request $request) {}
    
    public function restore($id) {}
    
    public function _getRoles() {
        return [
            //'list' => Auth::user()->can('notification list'),
            //'create' => Auth::user()->can('notification create'),
            //'edit' => Auth::user()->can('notification edit'),
            //'delete' => Auth::user()->can('notification delete'),
        ];
    }
}
