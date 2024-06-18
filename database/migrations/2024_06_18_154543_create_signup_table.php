//SignUp

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('signup', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_number')->unique();
            $table->string('otp')->nullable();
            $table->string('name')->nullable();
            $table->timestamp('otp_timestamp')->nullable();
            $table->integer('otp_request_count')->default(0);
            $table->timestamp('first_request_timestamp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signup');
    }
};




