<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeFilesAttachable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_downloads', function (Blueprint $table) {
            $table->integer('attachable_id')->nullable();
            $table->string('attachable_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_downloads', function (Blueprint $table) {
            $table->dropColumn('attachable_id');
            $table->dropColumn('attachable_type');
        });
    }
}
