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
        Schema::table('student_guardians', function (Blueprint $table) {
            $table->string('whatsapp_number')->after('guardian_contact')->nullable();
            $table->boolean('is_wa_active')->after('whatsapp_number')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_guardians', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'is_wa_active']);
        });
    }
};
