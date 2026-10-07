<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\System;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['email' => 'lucascastro121295@gmail.com', 'name' => 'Lucas Castro', 'role_id' => 1, 'system_id' => null],
        ];

        foreach (System::orderBy('slug')->get() as $system) {
            $emailSlug = Str::slug($system->slug, '_');

            $users[] = [
                'email' => "admin_{$emailSlug}@gmail.com",
                'name' => "Admin {$system->name}",
                'role_id' => 2,
                'system_id' => $system->id,
            ];

            $users[] = [
                'email' => "user_{$emailSlug}@gmail.com",
                'name' => "Usuário {$system->name}",
                'role_id' => 3,
                'system_id' => $system->id,
            ];
        }

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => 'mudar123',
                    'role_id' => $data['role_id'],
                    'system_id' => $data['system_id'],
                ]
            );

            $user->markEmailAsVerified();

            $role = match ($data['role_id']) {
                1 => 'super_admin',
                2 => 'admin',
                3 => 'user',
            };
            $systemLabel = $data['system_id'] ? System::find($data['system_id'])?->name : 'todos';
            $this->command?->info("User '{$user->email}' ({$role}) - Sistema: {$systemLabel} created/updated successfully.");
        }
    }
}
