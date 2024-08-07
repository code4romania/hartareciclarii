<?php

declare(strict_types=1);

use App\Models\Issue;
use App\Models\IssueType;
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
        Schema::create('issue_issue_type', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(IssueType::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Issue::class)->constrained()->onDelete('cascade');
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_issue_type');
    }
};
