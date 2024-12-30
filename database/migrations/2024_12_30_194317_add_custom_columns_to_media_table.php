<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // Add custom fields
            $table->string('alt_text')->nullable()->after('order_column');
            $table->string('folder')->nullable()->after('alt_text');
            $table->string('type')->nullable()->after('folder');
            $table->text('caption')->nullable()->after('type');
            $table->text('description')->nullable()->after('caption');
            
            // Change these to JSON columns instead of strings with default values
            $table->json('manipulations')->default(DB::raw('(JSON_ARRAY())'))->change();
            $table->json('custom_properties')->default(DB::raw('(JSON_ARRAY())'))->change();
            $table->json('generated_conversions')->default(DB::raw('(JSON_ARRAY())'))->change();
            $table->json('responsive_images')->default(DB::raw('(JSON_ARRAY())'))->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn([
                'alt_text',
                'folder',
                'type',
                'caption',
                'description'
            ]);
            
            // Revert the default values if needed
            $table->string('manipulations')->change();
            $table->string('custom_properties')->change();
            $table->string('generated_conversions')->change();
            $table->string('responsive_images')->change();
        });
    }
};
