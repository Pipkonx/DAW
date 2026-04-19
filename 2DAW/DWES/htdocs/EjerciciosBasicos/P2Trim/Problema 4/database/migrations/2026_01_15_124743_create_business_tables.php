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
        Schema::create('provinces', function (Blueprint $table) {
            $table->string('code', 2)->primary();
            $table->string('name');
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('cif')->unique();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->string('bank_account');
            $table->string('country');
            $table->string('currency')->default('EUR');
            $table->decimal('monthly_fee', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('concept');
            $table->date('emission_date');
            $table->decimal('amount', 10, 2);
            $table->enum('is_paid', ['S', 'N'])->default('N');
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('invoice_path')->nullable();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->string('contact_person');
            $table->string('contact_phone');
            $table->string('contact_email');
            $table->text('description');
            $table->string('address');
            $table->string('city');
            $table->string('postal_code', 5);
            $table->string('province_code', 2);
            $table->enum('status', ['pending', 'done', 'cancelled'])->default('pending');
            $table->foreignId('operator_id')->constrained('users')->onDelete('cascade');
            $table->date('completion_date')->nullable();
            $table->text('previous_notes')->nullable();
            $table->text('posterior_notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->foreign('province_code')->references('code')->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('fees');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('provinces');
    }
};
