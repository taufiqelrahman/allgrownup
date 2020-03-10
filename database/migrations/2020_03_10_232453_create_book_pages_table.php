<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('occupation_id');
            $table->tinyInteger('order');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('occupation_id')
                ->references('id')->on('occupations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_pages');
    }
}
