<?php

namespace App\Repositories\DB;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\DB;

class TaskRepository implements TaskRepositoryContract
{

    public function list(array $filters = [], int $perPage): LengthAwarePaginator
    {   
        return Task::query()
        ->filter($filters)  
        ->with(['assignedUser', 'creator'])
        ->orderBy('created_at')
        ->paginate($perPage);
    }


    /**
     * Find task by ID with needed relationships
     * available relations ['assignedUser', 'creator', 'dependencies']
     */
    public function findById(int $id, array $with = []): Task
    {
        $query = Task::query();
        
        if (!empty($with)) {
            $query->with($with);
        }
    
         $task = $query->find($id);

         if (!$task) {
            throw new NotFoundException('Task not found');
        }
        return $task;
    }


    /**
     * Create a new task
     */
    public function create(array $data): Task
    {
        $task = Task::create($data);
        return $task->load(['assignedUser', 'creator']);
    }

    /**
     * Update task
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh(['assignedUser', 'creator']);
    }


    /**
     * Add dependencies to a task
     */
    public function addDependencies(Task $task, array $dependencyIds): void
    {
        // Sync will add new dependencies and keep existing ones
        $task->dependencies()->syncWithoutDetaching($dependencyIds);
    }


    /**
     * Get all  dependents for a given task
     */
    public function getAllDependents(int $taskId): array
    {
        // Use recursive CTE to get all dependents
        $results = DB::select("
            WITH RECURSIVE dependent_chain AS (
                -- Base case: direct dependents
                SELECT task_id, depends_on_task_id, 1 as depth
                FROM task_dependencies
                WHERE depends_on_task_id = ?
                
                UNION ALL
                
                -- Recursive case: dependents of dependents
                SELECT td.task_id, td.depends_on_task_id, dc.depth + 1
                FROM task_dependencies td
                INNER JOIN dependent_chain dc ON td.depends_on_task_id = dc.task_id
                WHERE dc.depth < 100 -- Prevent infinite loops (safety)
            )
            SELECT DISTINCT task_id
            FROM dependent_chain
        ", [$taskId]);

        // Extract task IDs and return as array
        return array_map(fn($row) => $row->task_id, $results);
    }
}