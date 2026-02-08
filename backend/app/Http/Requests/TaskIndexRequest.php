<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TaskStatuses;
use Illuminate\Validation\Rule;

class TaskIndexRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            // Status filter - must be valid enum value
            'status' => [
                'nullable',
                'string',
                Rule::in(TaskStatuses::values()),
            ],
            

            'assigned_to' => [
                'nullable',
                'integer', 
                'min:1'
            ],
            

            'due_date_from' => [
                'nullable',
                'date_format:Y-m-d'
            ],
            
            'due_date_to' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

}