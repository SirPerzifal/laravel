<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->unsignedBigInteger('tipe_kelas_id')->after('dosen_id');
            $table->foreign('tipe_kelas_id')->references('id')->on('tipe_kelas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['tipe_kelas_id']);
            $table->dropColumn('tipe_kelas_id');
        });
    }
};
