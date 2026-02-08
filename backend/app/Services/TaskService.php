<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryContract;
use App\Exceptions\ValidationException;
use App\Enums\UserRoles;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    public function __construct(
        private TaskRepositoryContract $taskRepository
    ) {}

    /**
     * Get paginated list of tasks based on user role
     * 
     * Managers: See all tasks with applied filters
     * Users: See only their assigned tasks
     */
    public function list(User $user, array $filters = [], int $perPage): LengthAwarePaginator
    {
        if ($user->role == UserRoles::USER->value) {
            $filters['assigned_to'] = $user->id;
        }
        
        return $this->taskRepository->list($filters, $perPage);
    }

    /**
     * Get task by ID
     */
    public function getById(int $id): Task
    {
        return $this->taskRepository->findById($id,['dependencies','creator','assignedUser']);
    }

    /**
     * Create a new task
     */
    public function create(array $data, int $createdBy): Task
    {
        // append creator id
        $data['created_by'] = $createdBy;
        return $this->taskRepository->create($data);
    }

    /**
     * Update task details (title, description, due_date, assigned_to)
     */
    public function update(Task $task, array $data): Task
    {
        return $this->taskRepository->update($task, $data);
    }

    /**
     * Update task status
     */
    public function updateStatus(Task $task, string $newStatus): Task
    {
        return $this->taskRepository->update($task, ['status' => $newStatus]);
    }


    /**
     * Add dependencies to a task

    */
    public function addDependencies(Task $task, array $dependencyIds): Task
    {
        $this->taskRepository->addDependencies($task, $dependencyIds);

        return $this->taskRepository->findById($task->id,['dependencies','creator','assignedUser']);
    }
}