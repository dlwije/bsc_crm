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
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo', 100)->nullable();
            $table->string('lead_serialNo', 100)->nullable();
            $table->date('date')->nullable();
            $table->string('customerName', 100)->nullable();
            $table->string('companyName', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('industry', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('responsiblePerson', 100)->nullable();
            $table->string('username', 100)->nullable();
            $table->dateTime('datetime')->nullable();

            $table->foreign('lead_serialNo')->references('serialNo')->on('leads')->onUpdate('cascade');
            $table->foreign('industry')->references('Name')->on('industries')->onUpdate('cascade');
            $table->foreign('responsiblePerson')->references('userName')->on('users')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followups');
    }
};
