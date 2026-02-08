<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use App\Rules\TaskCanAcceptDependencies;
use App\Rules\TaskWithNoCircularDependency;
use App\Repositories\DB\TaskRepository;
class TaskDependenciesAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dependencies' => [
                'required',
                'array',
                'min:1',
                new TaskCanAcceptDependencies($this->route('task')),
                new TaskWithNoCircularDependency($this->route('task'),app(TaskRepository::class)),
                
            ],
            'dependencies.*' => [
                'required',
                'integer',
                'exists:tasks,id',
            ],
        ];
    }
}
