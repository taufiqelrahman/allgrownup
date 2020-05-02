<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('children', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->string('name');
            $table->string('cover');
            $table->string('language');
            $table->string('occupations');
            $table->enum('gender', ['boy', 'girl']);
            $table->enum('age', ['toddler', 'kid']);
            $table->enum('skin', ['light', 'medium', 'dark']);
            $table->enum('hair', ['short', 'hijab', 'curly']);
            $table->string('birthdate')->nullable();
            $table->longText('message');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('order_id')
                ->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('children');
    }
}
