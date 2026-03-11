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
        Schema::create('tb_vendedor', function (Blueprint $table) {
            
            $table->foreignId('id_vendedor')->primary()->constrained('users');
            $table->decimal('comissao', 5, 2);
            $table->string('cpf', 11);
            $table->text('observacoes')->nullable();
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
        Schema::dropIfExists('tb_vendedor');
    }
};
