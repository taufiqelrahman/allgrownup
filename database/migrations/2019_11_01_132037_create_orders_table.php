<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_number')->index();
            $table->string('shipping_number')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('address_id');
            $table->string('payment_type')->nullable();
            $table->decimal('total');
            $table->string('shipping_method');
            $table->decimal('shipping_rate');
            $table->string('pages'); // string of id
            $table->string('child_name')->nullable();
            $table->enum('child_gender', ['boy', 'girl']);
            $table->string('child_type')->nullable();
            $table->enum('book_format', ['hard', 'soft']);
            $table->string('message')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')
                ->references('id')->on('users');
            $table->foreign('address_id')
                ->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
