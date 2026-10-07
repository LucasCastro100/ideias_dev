<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Plan;
use App\Models\ClientPlan;
use App\Models\FinancialCategory;
use App\Models\AccountType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user_cliente@gmail.com')->first();
        if (!$user) {
            $this->command?->warn('Usuário user_cliente@gmail.com não encontrado. Execute AdminUserSeeder primeiro.');
            return;
        }

        $plans = Plan::factory()->count(3)->sequence(
            ['name' => 'Básico', 'value' => 99.90, 'billing_cycle' => 'monthly'],
            ['name' => 'Profissional', 'value' => 199.90, 'billing_cycle' => 'monthly'],
            ['name' => 'Enterprise', 'value' => 499.90, 'billing_cycle' => 'yearly'],
        )->create(['user_id' => $user->id]);

        $clients = Client::factory()->count(5)->create(['user_id' => $user->id]);

        foreach ($clients as $client) {
            ClientPlan::create([
                'user_id' => $user->id,
                'client_id' => $client->id,
                'plan_id' => $plans->random()->id,
                'start_date' => now()->subMonths(rand(1, 6)),
                'active' => true,
                'adjusted_value' => rand(0, 1) ? null : rand(50, 200),
            ]);
        }

        if (User::where('email', 'user_financeiro@gmail.com')->exists()) {
            $finUser = User::where('email', 'user_financeiro@gmail.com')->first();

            AccountType::factory()->count(3)->sequence(
                ['name' => 'Banco Corrente'],
                ['name' => 'Dinheiro'],
                ['name' => 'Cartão de Crédito'],
            )->create(['user_id' => $finUser->id]);

            FinancialCategory::factory()->count(4)->sequence(
                ['name' => 'Alimentação', 'type' => 'expense', 'color' => '#ef4444'],
                ['name' => 'Salário', 'type' => 'income', 'color' => '#22c55e'],
                ['name' => 'Transporte', 'type' => 'expense', 'color' => '#f97316'],
                ['name' => 'Freelas', 'type' => 'income', 'color' => '#3b82f6'],
            )->create(['user_id' => $finUser->id]);
        }

        $this->command?->info('Dados de demonstração criados com sucesso!');
    }
}
