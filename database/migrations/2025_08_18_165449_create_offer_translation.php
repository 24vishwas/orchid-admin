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
        Schema::create('offer_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5); // en, kn, etc.
            $table->string('title');
            $table->timestamps();
        });
        Schema::table('offers', function (Blueprint $table) {
            // for example: remove old title column
            $table->dropColumn('title');
    
            // or add new columns
            $table->boolean('is_translatable')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
          Schema::table('offers', function (Blueprint $table) {
        $table->dropColumn('is_translatable');
        $table->string('title'); // add back if you dropped it
    });
        Schema::dropIfExists('offer_translations');
    }
};
