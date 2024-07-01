<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');

            $table->time('started_at')->nullable();
            $table->time('finished_at')->nullable();
            $table->time('reduce')->nullable();
            $table->time('vacation')->nullable();
            $table->time('home_work')->nullable();
            $table->date('day')->default(DB::raw('CURRENT_DATE'));
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'day', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
