<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emprego_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->text('description')->nullable();
            $table->string('plan')->default('free');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('emprego_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('emprego_companies')->nullOnDelete();
            $table->string('title');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status')->default('aberta');
            $table->timestamps();
        });

        Schema::create('emprego_job_seekers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('summary')->nullable();
            $table->text('skills')->nullable();
            $table->timestamps();
        });

        Schema::create('emprego_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('emprego_jobs')->cascadeOnDelete();
            $table->foreignId('job_seeker_id')->constrained('emprego_job_seekers')->cascadeOnDelete();
            $table->string('status')->default('pendente');
            $table->dateTime('applied_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprego_applications');
        Schema::dropIfExists('emprego_job_seekers');
        Schema::dropIfExists('emprego_jobs');
        Schema::dropIfExists('emprego_companies');
    }
};
