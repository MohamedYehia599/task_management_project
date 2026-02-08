<?php

namespace App\Models;

use App\Enums\TaskStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assigned_to',
        'created_by',
    ];

      /**
     * Default values for new model instances
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // /**
    //  * Available task statuses
    //  */
    //use Task Statuses Enum

    /**
     * Get the user this task is assigned to
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this task
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the tasks that this task depends on
     * (Tasks that must be completed before this one)
     */
    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Task::class,
            'task_dependencies',
            'task_id',
            'depends_on_task_id'
        )->withTimestamps();
    }


    /**
     * Check if task can be marked as completed
     */
    public function canBeCompleted(): bool
    {
        $dependencies = $this->dependencies;
        
        if ($dependencies->isEmpty()) {
            return true;
        }
        //check if all direct dependencies are completed
        foreach ($dependencies as $dependency) {
            if ($dependency->status !== TaskStatuses::COMPLETED->value) {
                return false;
            }
        }
        
        return true;
    }

    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $key => $value) {
            match($key) {
                'assigned_to' => $query->where('assigned_to', $value),
                'status' => $query->where('status', $value),
                'due_date_from' => $query->whereDate('due_date', '>=', $value),
                'due_date_to' => $query->whereDate('due_date', '<=', $value),
            };
        }
        
        return $query;
    }

   
}