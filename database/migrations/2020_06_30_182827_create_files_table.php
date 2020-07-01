<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->unsignedBigInteger('storage_id');

            $table->string('uniq_id');
            $table->string('name');
            $table->string('extension');
            $table->integer('size');

            $table->string('public_url')->unique()->nullable();
            $table->timestamps();

            $table->foreign('folder_id')
                ->references('id')
                ->on('folders')
                ->onDelete('set null');

            $table->foreign('storage_id')
                ->references('id')
                ->on('storages')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
