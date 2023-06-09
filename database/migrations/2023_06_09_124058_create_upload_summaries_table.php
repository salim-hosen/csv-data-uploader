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
        Schema::create('upload_summaries', function (Blueprint $table) {
            $table->id();
            $table->string("job_batch_id");
            $table->bigInteger("total_data")->default(0);
            $table->bigInteger("total_successful")->default(0);
            $table->bigInteger("total_duplicate")->default(0);
            $table->bigInteger("total_invalid")->default(0);
            $table->bigInteger("total_incomplete")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_summaries');
    }
};
