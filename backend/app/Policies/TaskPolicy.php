<?php

namespace App\Policies;
use App\Models\Task;
use App\Models\User;
use App\Enums\UserRoles;

class TaskPolicy
{
    /**
     * Determine if the user can view the task.
     * 
     * Managers can view all tasks
     * Users can view only their assigned tasks
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->role === UserRoles::MANAGER->value) {
            return true;
        }

        return $task->assigned_to === $user->id;
    }

    /**
     * Determine if the user can create tasks.
     * 
     * Only managers can create tasks
     */
    public function create(User $user): bool
    {
        return $user->role === UserRoles::MANAGER->value;
    }

    /**
     * Determine if the user can update the task.
     * 
     * Only managers can update task details (title, description, due_date, assigned_to)
     */
    public function update(User $user,Task $task ): bool
    {
        return $user->role === UserRoles::MANAGER->value;
    }

    /**
     * Determine if the user can update the task status.
     * 
     * Managers can update any task status
     * Users can update status only for tasks assigned to them
     */
    public function updateStatus(User $user, Task $task): bool
    {
        if ($user->role === UserRoles::MANAGER->value) {
            return true;
        }

        return $task->assigned_to === $user->id;
    }



    /**
     * Determine if the user can add dependencies to the task.
     * 
     * Only managers can add dependencies
     */
    public function addDependencies(User $user, Task $task): bool
    {
        return $user->role === UserRoles::MANAGER->value;
    }

}