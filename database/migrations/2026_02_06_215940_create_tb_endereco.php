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
        Schema::create('tb_endereco', function (Blueprint $table) {
            $table->id();
            $table->string('cep', 8);
            $table->string('rua', 255);
            $table->string('numero', 30);
            $table->string('complemento', 255)->nullable();
            $table->string('bairro', 255);
            $table->string('cidade', 255);
            $table->string('estado', 2);
            $table->boolean('removido')->default(false);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            // Já estava pode comentar ou excluir $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_endereco');
    }
};
