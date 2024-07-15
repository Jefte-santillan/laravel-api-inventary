<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->integer('number_serie')->nullable();
            $table->unsignedBigInteger('created_identity_id');
            $table->unsignedBigInteger('operation_type_id');
            $table->timestamps();
            $table->integer('deleted_at')->default(0);
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('created_identity_id')->references('id')->on('identities');
            $table->foreign('operation_type_id')->references('id')->on('type_operations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operations');
    }
};
