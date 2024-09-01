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
        Schema::create('stockers', function (Blueprint $table) {
            $table->id('idStock');
            $table->integer('idPro'); // PK table PRODUIT
            $table->integer('idCont'); // PK table CONTENEUR
            $table->integer('idPr'); // PK table PROVENANCE
            $table->dateTime('date')->useCurrent();
            $table->integer('quantite');
            $table->integer('vie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockers');
    }
};
