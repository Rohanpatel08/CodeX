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
        Schema::table('submissions', function (Blueprint $table) {
            $table->text('output')->nullable();
            $table->string('status')->nullable();
            $table->decimal('execution_time', 8, 4)->nullable();
            $table->integer('memory_usage')->nullable();
            $table->integer('exit_code')->nullable();
            $table->string('judge0_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn([
                'output', 
                'status', 
                'execution_time', 
                'memory_usage', 
                'exit_code',
                'judge0_token'
            ]);
        });
    }
};
