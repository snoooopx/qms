<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group_name');
            $table->string('batch_id', 32);
            $table->string('search_criteria')->nullable();
            $table->string('attempts');
            $table->unsignedInteger('customer_id');
            $table->enum('status',['success', 'fail', 're_queued']);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_log');
    }
}
