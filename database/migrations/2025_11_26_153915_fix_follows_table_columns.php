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
    Schema::table('follows', function (Blueprint $table) {
        if (Schema::hasColumn('follows', 'user_id')) {
            $table->renameColumn('user_id', 'following_id');
        }

        if (Schema::hasColumn('follows', 'followed_id')) {
            $table->renameColumn('followed_id', 'following_id');
        }
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
