<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Repositories\DB\TaskRepository;
use App\Models\Task;
class TaskWithNoCircularDependency implements ValidationRule
{

    public function __construct(private Task $task,private TaskRepository $taskRepository)
    {}
     /**
     * Run the validation rule.
     * 
     * Check if adding these dependencies would create a circular dependency.
     * 
     * Logic:
     * 1. Get targeted task all dependents 
     * 2. Check if any of the new dependencies are in that list
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $selfDependencies = array_filter($value, function($dependencyId) {
            return $dependencyId == $this->task->id;
        });
        
        if (!empty($selfDependencies)) {
            $fail('A task cannot depend on itself.');
            return; 
        }

        $allDependents = $this->taskRepository->getAllDependents($this->task->id);
        // Check if any of the new dependencies are already dependents
        $circularDependencies = array_intersect($value, $allDependents);
        if ($circularDependencies) {

            $fail(sprintf('These tasks would create a circular dependency: %s',
            implode(', ,', $circularDependencies)));
    }
}
}