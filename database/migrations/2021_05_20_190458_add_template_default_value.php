<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddTemplateDefaultValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_site', function ($table) {
            $table->text('template')->nullable()->default('template.php')->change();
            $table->text('template_name')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_site', function ($table) {
            $table->text('template')->nullable(false)->default('')->change();
            $table->text('template')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
}
