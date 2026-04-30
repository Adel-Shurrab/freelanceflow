<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->comment('Polymorphic flat comments on Projects, Tasks, Milestones, Proposals — no threading in v1');
            $table->id();
            $table->morphs('commentable');
            $table->foreignIdFor(User::class)->nullable()->constrained()->nullOnDelete();
            $table->text('body')->comment('max 5000 chars');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id', 'comments_user_id_index');
            $table->index('deleted_at', 'comments_deleted_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
