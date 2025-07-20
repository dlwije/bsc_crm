<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo', 100)->unique()->nullable();
            $table->date('date')->nullable();
            $table->string('customerName', 250)->nullable();
            $table->string('companyName', 250)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('industry_id', 100)->nullable();
            $table->string('leadSource_id', 100)->nullable();
            $table->string('leadStatus_id', 100)->nullable();
            $table->double('revenue', 40, 4)->nullable();
            $table->string('product_id', 100)->nullable();
            $table->string('description', 100)->nullable();
            $table->string('responsiblePerson_id', 100)->nullable();
            $table->string('username', 100)->nullable();
            $table->string('datetime', 100)->nullable();
            $table->string('updateusername', 100)->nullable();
            $table->string('updatedatetime', 100)->nullable();

            $table->foreign('industry_id')->references('id')->on('industries')->onUpdate('cascade');
            $table->foreign('leadSource_id')->references('id')->on('inquiry_source')->onUpdate('cascade');
            $table->foreign('leadStatus_id')->references('id')->on('lead_statuses')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade');
            $table->foreign('responsiblePerson_id')->references('id')->on('users')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
