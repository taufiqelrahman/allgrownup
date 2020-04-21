<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('occupation_id');
            $table->tinyInteger('page_number');
            $table->longText('english');
            $table->longText('indonesia');
            $table->string('style');
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
        Schema::dropIfExists('book_contents');
    }
}
