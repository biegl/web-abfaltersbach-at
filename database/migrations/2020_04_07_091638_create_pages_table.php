<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_site', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('navigation_id')->nullable();
            $table->text('seitentitel', 255);
            $table->text('keywords');
            $table->text('template', 50);
            $table->text('template_name', 45);
            $table->text('inhalt')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->text('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
