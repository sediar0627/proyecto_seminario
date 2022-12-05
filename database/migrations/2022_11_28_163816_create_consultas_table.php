<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 8);
            $table->foreignId('camara_id')->references('id')->on('camaras');
            $table->date('fecha');
            $table->boolean('soat_vigente')->nullable()->index();
            $table->boolean('rtm_vigente')->nullable()->index();
            $table->string('clase', 100)->nullable()->index();
            $table->string('marca', 100)->nullable()->index();
            $table->string('servicio', 100)->nullable()->index();
            $table->string('color', 100)->nullable()->index();
            $table->string('modelo', 100)->nullable()->index();
            $table->integer('estado')->default(1)->index();
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consultas');
    }
};
