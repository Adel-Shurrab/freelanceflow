<?php

use App\Models\User;
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
        Schema::create('team_members', function (Blueprint $table) {
            $table->comment('Agency Admin to Freelancer member mappings — OwnedByFreelancerScope uses this for Admin role');
            $table->id();
            $table->foreignIdFor(User::class, 'agency_user_id')->constrained()->cascadeOnDelete()->comment('The Admin/Agency account owner');
            $table->foreignIdFor(User::class, 'member_user_id')->constrained()->cascadeOnDelete()->comment('The Freelancer member belonging to the agency');
            $table->timestamp('created_at')->useCurrent()->comment('When the team member was added');

            $table->unique(['agency_user_id', 'member_user_id'], 'team_members_unique');

            $table->index('agency_user_id', 'team_members_agency_user_id_index');
            $table->index('member_user_id', 'team_members_member_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
