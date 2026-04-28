<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mapels', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->index('mapel_id');
        });

        Schema::create('mapel_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['mapel_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapel_teacher');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('mapels');
    }
};
