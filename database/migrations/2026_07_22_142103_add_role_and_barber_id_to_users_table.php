<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employee')->after('email'); // admin, employee
            $table->unsignedBigInteger('barber_id')->nullable()->after('role');
            
            $table->foreign('barber_id')->references('id')->on('barbers')->nullOnDelete();
        });
        
        // Transform the first user (the one created during installation) into an admin
        \DB::table('users')->where('id', 1)->update(['role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['barber_id']);
            $table->dropColumn(['role', 'barber_id']);
        });
    }
};
