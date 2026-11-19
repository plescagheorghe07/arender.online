<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('customer_phones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 40);
            $table->timestamps();
        });

        Schema::create('customer_emails', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->timestamps();
        });

        Schema::create('customer_addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('address');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->string('email')->nullable();
            $table->string('locale', 5)->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('customer_id');
            $table->dropColumn(['email', 'locale']);
        });

        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customer_emails');
        Schema::dropIfExists('customer_phones');
        Schema::dropIfExists('customers');
    }
};
