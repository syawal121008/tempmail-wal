<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempMailsTable extends Migration
{
    public function up()
{
    Schema::create('temp_mails', function (Blueprint $table) {
        $table->id();
        $table->string('email')->unique();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('ip_address')->nullable();
        $table->timestamp('last_checked_at')->nullable();
        $table->timestamp('expires_at');
        $table->timestamps();
        
        $table->index('email');
        $table->index('expires_at');
    });

    }

    public function down()
    {
        Schema::dropIfExists('temp_mails');
    }
}
