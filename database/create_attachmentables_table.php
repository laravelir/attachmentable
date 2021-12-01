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
            $table->string('disk', 32);
            $table->string('filename', 255);
            $table->string('title', 92)->nullable();
            $table->text('description')->nullable();
            $table->string('filetype', 512)->change();
            $table->string('group')->nullable()->after('key');

            $table->string('path');
            $table->string('key');
            $table->string('mime_type')->nullable();
            $table->string('size')->nullable();
            $table->longText('metadata')->nullable();
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
