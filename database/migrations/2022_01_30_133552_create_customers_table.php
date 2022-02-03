<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->unique();
            $table->string('date_created');
            $table->string('email')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('role');
            $table->string('username');
            $table->text('billing')->nullable();
            $table->text('shipping')->nullable();
            $table->string('avatar_url')->nullable();
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
        Schema::dropIfExists('customers');
    }
}
