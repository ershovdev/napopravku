<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('storage_id');
            $table->string('name');
            $table->string('uniq_id');
            $table->string('parent_id')->nullable();
            $table->timestamps();

            $table->foreign('storage_id')
                ->references('id')
                ->on('storages')
                ->onDelete('set null');

            $table->foreign('parent_id')
                ->references('id')
                ->on('folders')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folders');
    }
}
