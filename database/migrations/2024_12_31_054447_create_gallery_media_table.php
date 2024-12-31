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
        Schema::create('gallery_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_id')->constrained()->onDelete('cascade');
            $table->foreignId('media_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Prevent duplicate associations
            $table->unique(['gallery_id', 'media_id']);
        });

        // Add template column to galleries table if it doesn't exist
        if (!Schema::hasColumn('galleries', 'template')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->string('template')->default('grid')->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_media');
        
        // Remove template column from galleries table if it exists
        if (Schema::hasColumn('galleries', 'template')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->dropColumn('template');
            });
        }
    }
};
