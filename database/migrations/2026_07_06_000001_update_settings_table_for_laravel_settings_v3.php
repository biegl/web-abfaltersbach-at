<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['group']);
            $table->boolean('locked')->default(false)->change();
            $table->unique(['group', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['group', 'name']);
            $table->boolean('locked')->change();
            $table->index('group');
        });
    }
};
