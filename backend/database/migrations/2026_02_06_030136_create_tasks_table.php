<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TaskStatuses;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', [
                TaskStatuses::PENDING->value,
                TaskStatuses::COMPLETED->value,
                TaskStatuses::CANCELED->value,
            ])->default('pending');
                  
            $table->date('due_date');
            

            $table->foreignId('assigned_to')
                  ->constrained('users')
                  ->onDelete('restrict');
            
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('restrict');
            
            $table->timestamps();
            
            $table->index(['assigned_to', 'status', 'due_date']);
            $table->index(['status',  'due_date']);
            $table->index(['due_date']);
            $table->index(['created_at']);

        });
    }


};
