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
        Schema::create('tb_item', function (Blueprint $table) {
            
            $table->id();
            $table->foreignId('id_venda')->constrained('tb_venda');
            $table->foreignId('id_produto')->constrained('tb_produto');
            $table->decimal('valor', 10, 2)->default(0);
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
        Schema::dropIfExists('tb_item');
    }
};
