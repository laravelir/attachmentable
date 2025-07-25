<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('attachmentables', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('attachmentorable');
            $table->morphs('attachmentable');
            $table->string('disk', 15);
            $table->string('path');
            $table->string('original_name', 255);
            $table->string('name', 255)->unique();
            $table->text('description')->nullable();
            $table->string('key')->nullable();
            $table->string('group')->nullable();
            $table->longText('meta')->nullable(); // size ext mime , ...
            $table->softDeletes();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('attachmentables');
    }
};
