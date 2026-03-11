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
        Schema::create('tb_venda', function (Blueprint $table) {
            
            $table->id();
            $table->date('data');
            $table->foreignId('id_cliente')->constrained('tb_cliente');
            $table->foreignId('id_vendedor')->constrained('users');
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
        Schema::dropIfExists('tb_venda');
    }
};
