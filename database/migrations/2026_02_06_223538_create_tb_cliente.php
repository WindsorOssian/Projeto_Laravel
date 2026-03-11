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
        Schema::create('tb_cliente', function (Blueprint $table) {
            
            $table->id();
            $table->string('nome', 255);
            $table->string('email', 255);
            $table->foreignId('id_endereco')->constrained('tb_endereco');
            $table->foreignId('id_admin')->constrained('users');
            $table->boolean('removido')->default(false);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_cliente');
    }
};
