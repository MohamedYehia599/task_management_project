<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TaskStatuses;
use Illuminate\Validation\Rules\Enum;
use App\Rules\TaskCanBeCompleted;

class TaskStatusUpdateRequest extends FormRequest
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
            'status' => [
            'required',
            new Enum(TaskStatuses::class),
            new TaskCanBeCompleted($this->route('task'))
        ]
        ];
    }
}
