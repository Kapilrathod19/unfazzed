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
        Schema::create('service_whats_includeds', function (Blueprint $row) {
            $row->id();
            $row->unsignedBigInteger('service_id')->nullable();
            $row->text('title')->nullable();
            $row->timestamps();
            $row->softDeletes();
            $row->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_whats_includeds');
    }
};
