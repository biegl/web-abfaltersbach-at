<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
            $table->text('template')->nullable()->change();
            $table->text('template_name')->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        // Set default value after column modification
        DB::table('tbl_site')->whereNull('template')->update(['template' => 'template.php']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_site', function ($table) {
            $table->text('template')->nullable(false)->change();
            $table->text('template_name')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
}
