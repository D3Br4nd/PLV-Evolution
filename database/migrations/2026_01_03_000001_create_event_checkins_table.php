<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_checkins', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignUuid('membership_id')->constrained('memberships')->cascadeOnDelete();
            $table->foreignUuid('checked_in_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('checked_in_at')->useCurrent();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['event_id', 'membership_id']);
            $table->index(['event_id', 'checked_in_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_checkins');
    }
};


