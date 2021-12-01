<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachmentables', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->morphs('attachmentable');
            $table->string('disk', 15);
            $table->string('path');
            $table->string('original_name', 255);
            $table->string('name', 255)->unique();
            $table->text('description')->nullable();
            $table->string('key');
            $table->string('group')->nullable();
            $table->string('filetype', 512);
            $table->string('size')->nullable();
            $table->longText('meta')->nullable();
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
        Schema::dropIfExists('attachmentables');
    }
}
