<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryContract
{
    /**
     * Get paginated list of tasks with optional filters
     */
    public function list(array $filters = [], int $perPage ): LengthAwarePaginator;

    /**
     * Find task by ID with relationships
     */
    public function findById(int $id): ?Task;

    /**
     * Create a new task
     */
    public function create(array $data): Task;

    /**
     * Update task
     */
    public function update(Task $task, array $data): Task;

    /**
     * Add dependencies to a task
     */
    public function addDependencies(Task $task, array $dependencyIds): void;


    /**
     * Get all tasks that depend on a given task
     */
    public function getAllDependents(int $taskId): array;
}