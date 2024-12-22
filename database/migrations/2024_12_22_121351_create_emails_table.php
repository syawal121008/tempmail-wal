<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsTable extends Migration
{
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temp_mail_id')->constrained()->onDelete('cascade');
            $table->string('message_id')->nullable();
            $table->string('from');
            $table->string('from_name')->nullable();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->boolean('has_attachments')->default(false);
            $table->timestamp('received_at');
            $table->timestamps();
            
            $table->index(['temp_mail_id', 'received_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('emails');
    }
}
