<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavigationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_navigation', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('refID')->nullable();
            $table->integer('position');
            $table->tinyInteger('level');
            $table->text('name', 75);
            $table->text('linkname', 75);
            $table->enum('navianzeigen', ['Ja', 'Nein']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_navigation');
    }
}
