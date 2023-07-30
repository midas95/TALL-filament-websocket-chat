<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBizsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bizs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organization_id')->nullable();
            $table->string('sector_slug')->nullable();
            $table->integer('legal_form_id')->nullable();
            $table->smallInteger('order')->nullable();
            $table->string('short');
            $table->string('name');
            $table->boolean('open_air')->default(0);
            $table->date('active_since')->nullable();
            $table->date('closed_since')->nullable();
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
        Schema::dropIfExists('bizs');
    }
}
