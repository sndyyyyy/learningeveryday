<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // Kolom boolean untuk saklar Tampil/Sembunyi (default 1 / true)
            $table->boolean('is_show_explanation')->default(true)->after('explanation');
            // Kolom string untuk menampung link YouTube
            $table->string('explanation_link')->nullable()->after('is_show_explanation');
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['is_show_explanation', 'explanation_link']);
        });
    }
};