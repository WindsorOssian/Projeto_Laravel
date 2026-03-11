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
        //Up é para subir as tabelas
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('parent_user_id')->nullable()->after('id')->constrained('users');
            $table->boolean('is_admin')->nullable()->after('parent_user_id');
            $table->boolean('removido')->default(false)->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Down é tipo um rollback para apagar
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('parent_user_id');
            $table->dropColumn('parent_user_id');
            $table->dropColumn('is_admin');
            $table->dropColumn('removido');
        });
    }
};
