<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Requests\TaskStatusUpdateRequest;
use App\Http\Requests\TaskDependenciesAddRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskShowResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\TaskIndexRequest;
use App\Enums\TaskAllowedFilters;
use App\Traits\AllowedFiltersTrait;
class TaskController extends Controller
{
    use AllowedFiltersTrait;
    public function __construct(
        private TaskService $taskService
    ) {}

    /**
     * Display a paginated list of tasks
     * 
     * Managers: See all tasks with optional filters
     * Users: See only their assigned tasks
     * 
     * GET /api/tasks
     * Query params: ?filter[status]=pending&filter[due_date_from]=2026-02-01&sort=-due_date
     */
    public function index(TaskIndexRequest $request): AnonymousResourceCollection
    {

        $validated = $request->validated();
        $filters = $this->getAllowedFilters($validated, TaskAllowedFilters::values());

        $tasks = $this->taskService->list($request->user(), $filters, $request->input('per_page', 15));
        
        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created task
     * 
     * Only managers can create tasks
     * 
     * POST /api/tasks
     */
    public function store(TaskCreateRequest $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        $task = $this->taskService->create(
            $request->validated(),
            $request->user()->id
        );
        
        return response()->json([
            'message' => 'Task created successfully',
            'data' => new TaskResource($task)
        ], 201);
    }

    /**
     * Display the specified task with dependencies
     * 
     * Managers: Can view any task
     * Users: Can view only tasks assigned to them
     * 
     * GET /api/tasks/{task}
     */
    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);
        
        $task = $this->taskService->getById($task->id);

        return response()->json([
            'data' => new TaskShowResource($task)
        ]);
    }

    /**
     * Update task details (title, description, due_date, assigned_to)
     * 
     * Only managers can update task details
     * 
     * PATCH /api/tasks/{task}
     */
    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);
        $task = $this->taskService->update($task, $request->validated());
        
        return response()->json([
            'message' => 'Task updated successfully',
            'data' => new TaskResource($task)
        ]);
    }

    /**
     * Update task status
     * 
     * Managers: Can update any task status
     * Users: Can update status only for tasks assigned to them
     * 
     * PATCH /api/tasks/{task}/status
     */
    public function updateStatus(TaskStatusUpdateRequest $request, Task $task): JsonResponse
    {
        $this->authorize('updateStatus', $task);
        
        $task = $this->taskService->updateStatus($task, $request->validated()['status']);
        
        return response()->json([
            'message' => 'Task status updated successfully',
            'data' => new TaskResource($task)
        ]);
    }

    /**
     * Add dependencies to a task
     * 
     * Only managers can add dependencies
     * 
     * POST /api/tasks/{task}/dependencies
     */
    public function addDependencies(TaskDependenciesAddRequest $request, Task $task): JsonResponse
    {
        
        $this->authorize('addDependencies', $task);
        
        $task = $this->taskService->addDependencies($task, $request->validated()['dependencies']);
        
        return response()->json([
            'message' => 'Dependencies added successfully',
            'data' => new TaskShowResource($task)
        ]);
    }


}