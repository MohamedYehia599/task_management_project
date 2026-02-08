<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Task;
use App\Enums\TaskStatuses;

class TaskCanAcceptDependencies implements ValidationRule
{
    public function __construct(private Task $task)
    {}
    /**
     * Run the validation rule.
     * validates that dependencies cant be added after task is completed
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($this->task->status ===TaskStatuses::COMPLETED->value){
            $fail("Cannot add dependencies for completed task");
        }
    }
}
