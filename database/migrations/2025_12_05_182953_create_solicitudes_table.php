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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->integer('profesores_id')->index('fk_solicitudes_profesores');
            $table->integer('equipos_id')->nullable()->index('fk_solicitudes1');
            $table->integer('computadoras_id')->nullable()->index('fk_solicitudes_computadoras1');
            $table->integer('cantidad')->nullable();
            $table->string('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
