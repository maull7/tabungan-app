<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_status')->default('completed')->after('note');
            $table->string('payment_provider')->nullable()->after('payment_status');
            $table->string('payment_reference')->nullable()->after('payment_provider');
            $table->string('payment_token')->nullable()->after('payment_reference');
            $table->text('payment_url')->nullable()->after('payment_token');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_provider',
                'payment_reference',
                'payment_token',
                'payment_url',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
