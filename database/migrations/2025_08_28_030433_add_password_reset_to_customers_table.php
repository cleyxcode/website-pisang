<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_password_reset_to_customers_table.php

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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('reset_password_token')->nullable()->after('remember_token');
            $table->timestamp('reset_password_token_expires_at')->nullable()->after('reset_password_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['reset_password_token', 'reset_password_token_expires_at']);
        });
    }
};