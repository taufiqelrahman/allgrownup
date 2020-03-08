<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoryPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('story_pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('filepath');
            $table->enum('child_gender', ['boy', 'girl']);
            $table->string('child_type')->nullable();
            $table->boolean('dedication')->nullable();
            $table->longText('options')->nullable();
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
        Schema::dropIfExists('story_pages');
    }
}
