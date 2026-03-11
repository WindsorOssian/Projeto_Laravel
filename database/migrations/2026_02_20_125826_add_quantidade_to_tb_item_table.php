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
        Schema::table('tb_item', function (Blueprint $table) {
            $table->integer('quantidade')->default(1)->after('id_produto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('tb_item', function (Blueprint $table) {
            $table->dropColumn('quantidade');
        });
    }
};
