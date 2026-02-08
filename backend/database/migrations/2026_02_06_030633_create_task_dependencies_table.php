<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            //task id to be unique to ensure one to many relation between task and dependent
            $table->foreignId('task_id')
                  ->constrained('tasks')
                  
                  ->onDelete('restrict');
            
            // The task that this task depends on
            $table->foreignId('depends_on_task_id')
                  ->constrained('tasks')
                  ->onDelete('restrict');

            $table->timestamps();
            
            $table->unique(['task_id', 'depends_on_task_id']);
            $table->index('depends_on_task_id');
        });
    }


};
