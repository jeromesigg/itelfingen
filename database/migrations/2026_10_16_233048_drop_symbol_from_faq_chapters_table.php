<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Das Symbol der FAQ-Kapitel (Font-Awesome-Klasse) wird nicht mehr angezeigt.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faq_chapters', function (Blueprint $table) {
            $table->dropColumn('symbol');
        });
    }

    public function down(): void
    {
        Schema::table('faq_chapters', function (Blueprint $table) {
            $table->string('symbol')->nullable();
        });
    }
};
