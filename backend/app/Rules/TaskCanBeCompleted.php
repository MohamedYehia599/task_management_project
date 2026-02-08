<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Task;
use App\Enums\TaskStatuses;

class TaskCanBeCompleted implements ValidationRule
{
    public function __construct(private Task $task)
    {
    }

    /**
     * Run the validation rule.
     * 
     * Only validate if the new status is "completed".
     * Check if direct dependencies are completed.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Only check if trying to mark as completed and task status not equal completed
        if ($value === TaskStatuses::COMPLETED->value && $this->task->status !== TaskStatuses::COMPLETED) {
            if (!$this->task->canBeCompleted()) {
                $fail("Cannot mark task as completed. dependencies must be completed first");
            }
        }
    }
}