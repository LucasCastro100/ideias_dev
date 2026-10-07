<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // Jobs
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 30)->unique();
            $table->string('label');
            $table->timestamps();
        });

        // Users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->foreignId('role_id')->constrained('roles');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Personal Access Tokens (Sanctum)
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Teams (Jetstream)
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('name');
            $table->boolean('personal_team');
            $table->timestamps();
        });

        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->foreignId('user_id');
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'email']);
        });

        // TBR Categories
        Schema::create('tbr_categories', function (Blueprint $table) {
            $table->string('id', 12)->primary();
            $table->string('slug')->unique();
            $table->string('label');
            $table->string('modality_level');
            $table->string('question_level');
            $table->string('dp_level');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // TBR Assessment Questions
        Schema::create('tbr_assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->string('level', 20);
            $table->string('modality_slug', 10);
            $table->string('object_name');
            $table->string('image')->default('');
            $table->boolean('mission')->default(false);
            $table->json('criteria');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['level', 'modality_slug', 'object_name']);
        });

        // TBR Modalities
        Schema::create('tbr_modalities', function (Blueprint $table) {
            $table->id();
            $table->string('level', 20);
            $table->string('config_id', 12);
            $table->string('slug', 10);
            $table->string('label');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['level', 'slug']);
        });

        // TBR Events
        Schema::create('tbr_events', function (Blueprint $table) {
            $table->string('id', 12)->primary();
            $table->string('name');
            $table->date('date');
            $table->boolean('status')->default(false);
            $table->json('location')->nullable();
            $table->json('ranking_config')->nullable();
            $table->timestamps();
        });

        // TBR DP Missions
        Schema::create('tbr_dp_missions', function (Blueprint $table) {
            $table->id();
            $table->string('dp_level', 30);
            $table->string('mission_title');
            $table->string('description')->default('');
            $table->string('image')->default('');
            $table->json('items');
            $table->string('depends_on')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('year')->default(date('Y'));
            $table->timestamps();

            $table->unique(['year', 'dp_level', 'mission_title']);
        });

        // TBR Teams
        Schema::create('tbr_teams', function (Blueprint $table) {
            $table->string('id', 12)->primary();
            $table->string('event_id', 12);
            $table->foreign('event_id')->references('id')->on('tbr_events')->cascadeOnDelete();
            $table->string('name');
            $table->string('category_slug');
            $table->string('representative_name', 100)->nullable();
            $table->string('representative_email', 150)->nullable();
            $table->string('representative_phone', 20)->nullable();
            $table->decimal('total_score', 10, 2)->default(0);
            $table->timestamps();
        });

        // TBR Team Modality Scores
        Schema::create('tbr_team_modality_scores', function (Blueprint $table) {
            $table->id();
            $table->string('team_id', 12);
            $table->foreign('team_id')->references('id')->on('tbr_teams')->cascadeOnDelete();
            $table->string('modality_slug', 10);
            $table->string('round', 5)->nullable();
            $table->json('scores');
            $table->decimal('total', 10, 2)->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'modality_slug', 'round']);
        });

        // Default roles
        DB::table('roles')->insert([
            ['id' => 1, 'slug' => 'super_admin', 'label' => 'Super Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'slug' => 'admin', 'label' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'slug' => 'user', 'label' => 'Usuário', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tbr_team_modality_scores');
        Schema::dropIfExists('tbr_teams');
        Schema::dropIfExists('tbr_dp_missions');
        Schema::dropIfExists('tbr_events');
        Schema::dropIfExists('tbr_modalities');
        Schema::dropIfExists('tbr_assessment_questions');
        Schema::dropIfExists('tbr_categories');
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
