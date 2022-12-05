<?php

use App\Models\Camara;
use App\Models\Lugar;
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
        Schema::create('camaras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lugar_id')->references('id')->on('lugares');
            $table->string('serial', 100)->unique();
            $table->timestamps();
        });

        $lugar = Lugar::create([
            'nombre' => 'Kilometro 12',
        ]);

        Camara::create([
            'lugar_id' => $lugar->id,
            'serial' => 'DS-2CD1047GO-L',
        ]);

        Camara::create([
            'lugar_id' => $lugar->id,
            'serial' => 'IDS-2CD74C5G0-IZS',
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('camaras');
    }
};
