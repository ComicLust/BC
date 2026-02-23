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
        Schema::table('backlinks', function (Blueprint $table) {
            $table->string('rel_attribute')->nullable()->after('status');
            $table->string('anchor_text')->nullable()->after('rel_attribute');
            $table->boolean('is_indexed')->default(false)->after('anchor_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backlinks', function (Blueprint $table) {
            $table->dropColumn(['rel_attribute', 'anchor_text', 'is_indexed']);
        });
    }
};
