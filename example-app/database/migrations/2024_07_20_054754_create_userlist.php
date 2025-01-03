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
        Schema::create('userlist', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->text('email')->unique();
            $table->text('address')->nullable();       
            $table->text('contact')->nullable();
            $table->text('user_img')->nullable();
            $table->timestamps();
            // $table->timestamp('created_at')->nullable();
            // $table->timestamp('modified_at')->nullable();                
        });
    }

    /** 
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('userlist');
    }
};
