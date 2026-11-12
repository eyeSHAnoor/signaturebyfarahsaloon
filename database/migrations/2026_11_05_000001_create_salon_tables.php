<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->decimal('price', 8, 2);
            $table->string('image_url')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('stylists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('title')->default('Senior Stylist');
            $table->text('bio')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('stylist_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 30);
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status', 20)->default('pending')->index();
            $table->decimal('price', 8, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['stylist_id', 'appointment_date']);
            $table->index(['user_id', 'appointment_date']);
        });

        Schema::create('appointment_slot_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stylist_id')->constrained()->cascadeOnDelete();
            $table->date('appointment_date');
            $table->time('slot_start');
            $table->unique(['stylist_id', 'appointment_date', 'slot_start'], 'stylist_slot_unique');
        });

        Schema::create('salon_gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('image_url');
            $table->string('before_image_url')->nullable();
            $table->string('after_image_url')->nullable();
            $table->string('alt_text')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('salon_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_title')->nullable();
            $table->text('quote');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('image_url')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('salon_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salon_settings');
        Schema::dropIfExists('salon_testimonials');
        Schema::dropIfExists('salon_gallery_items');
        Schema::dropIfExists('appointment_slot_reservations');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('stylists');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
