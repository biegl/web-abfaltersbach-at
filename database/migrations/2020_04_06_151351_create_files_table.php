<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_downloads', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('navID')->nullable();
            $table->integer('position');
            $table->text('title', 255);
            $table->text('file', 255);
            $table->integer('file_size')->nullabe();
            $table->text('file_orig_name', 255)->nullabe();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_downloads');
    }
}
