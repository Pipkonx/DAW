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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');          // Nombre del producto
            $table->text('description')->nullable();     // Descripción , nullable par que pueda ser opcional
            $table->decimal('price', 10, 2); // Precio
            $table->timestamps();
            // $table->unique(['name', 'user_id']); // Para que un usuario no tenga dos productos con el mismo nombre
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
