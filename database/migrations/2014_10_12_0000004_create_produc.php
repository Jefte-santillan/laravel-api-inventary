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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('created_identity_id');
            $table->integer('quantity')->default(0);
            $table->timestamps();
            $table->integer('deleted_at')->default(0);
            $table->unsignedBigInteger('product_status_id');
            $table->foreign('product_status_id')->references('id')->on('product_status');
            $table->foreign('created_identity_id')->references('id')->on('identities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
