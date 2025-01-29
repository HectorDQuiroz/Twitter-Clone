<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que recibe la notificación
            $table->string('notifiable_type'); // Tipo de modelo al que pertenece el notifiable_id (User, Post, etc.)
            $table->unsignedBigInteger('notifiable_id'); // ID del modelo que origina la notificación
            $table->string('type'); // Tipo de notificación (like, comment, follow)
            $table->text('data'); // Datos adicionales de la notificación
            $table->timestamp('read_at')->nullable(); // Fecha de lectura de la notificación
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};