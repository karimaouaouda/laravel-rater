<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists(Config::get("rater.table_name"));
        Schema::create(Config::get("rater.table_name"), function (Blueprint $table) {
            $table->id();

            $table->string("rater_type");
            $table->bigInteger("rater_id");

            $table->string("target_type");
            $table->bigInteger("target_id");

            $table->tinyInteger(Config::get("rater.amount_key"), unsigned:true);

            $table->text(Config::get('rater.comment_key') )->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Config::get("rater.table_name"));
    }
};
