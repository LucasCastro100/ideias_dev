<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $systems = [
            ['slug' => 'vagas-emprego', 'name' => 'Vagas de Emprego', 'description' => 'Portal de empregos que conecta empresas e candidatos.'],
            ['slug' => 'gestao-escolar', 'name' => 'Gestão Escolar / EAD', 'description' => 'Gestão acadêmica: turmas, alunos e faturas.'],
            ['slug' => 'ordem-servico', 'name' => 'Ordem de Serviço', 'description' => 'Controle de ordens de serviço e clientes de assistência técnica.'],
            ['slug' => 'email-marketing', 'name' => 'Email Marketing', 'description' => 'Listas de contatos e campanhas de email.'],
            ['slug' => 'marketing-multinivel', 'name' => 'Marketing Multinível (MMN)', 'description' => 'Rede de membros e pagamentos em cascata.'],
            ['slug' => 'gestao-advocacia', 'name' => 'Sistema para Advocacia', 'description' => 'Clientes e processos jurídicos.'],
            ['slug' => 'corridas-mobilidade', 'name' => 'Corridas / Mobilidade', 'description' => 'Motoristas e corridas sob demanda.'],
            ['slug' => 'rede-social', 'name' => 'Rede Social', 'description' => 'Publicações e grupos de uma comunidade.'],
            ['slug' => 'armazenamento-nuvem', 'name' => 'Armazenamento em Nuvem', 'description' => 'Pastas e arquivos com compartilhamento.'],
            ['slug' => 'pdv-vendas', 'name' => 'Vendas / PDV', 'description' => 'Produtos e vendas de ponto de venda.'],
            ['slug' => 'restaurante-mesas', 'name' => 'Restaurante — Controle de Mesas', 'description' => 'Mesas e pedidos de um restaurante.'],
            ['slug' => 'pizzaria-delivery', 'name' => 'Pizzaria Delivery', 'description' => 'Catálogo e pedidos de uma pizzaria.'],
            ['slug' => 'central-suporte', 'name' => 'Central de Suporte / Tickets', 'description' => 'Departamentos e tickets de suporte.'],
            ['slug' => 'site-institucional-cms', 'name' => 'Site Institucional / CMS', 'description' => 'Páginas e leads de um site institucional.'],
            ['slug' => 'gestao-arquivos-clientes', 'name' => 'Portal de Arquivos com Clientes', 'description' => 'Compartilhamento de arquivos com clientes.'],
            ['slug' => 'sistema-clinica', 'name' => 'Sistema para Clínicas', 'description' => 'Pacientes e consultas de uma clínica.'],
            ['slug' => 'controle-empresarial-nfe', 'name' => 'Controle Empresarial (RH + NFe)', 'description' => 'Produtos e vendas com emissão de NFe.'],
            ['slug' => 'faturamento-hospedagem', 'name' => 'Faturamento de Hospedagem', 'description' => 'Clientes e faturas de hospedagem.'],
            ['slug' => 'marketplace-leiloes', 'name' => 'Marketplace com Leilões', 'description' => 'Anúncios e lances de um marketplace.'],
            ['slug' => 'loja-virtual', 'name' => 'Loja Virtual / E-commerce', 'description' => 'Produtos e pedidos de uma loja virtual.'],
        ];

        foreach ($systems as $system) {
            DB::table('systems')->updateOrInsert(
                ['slug' => $system['slug']],
                $system + ['active' => true, 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }

    public function down(): void
    {
        DB::table('systems')->whereIn('slug', [
            'vagas-emprego', 'gestao-escolar', 'ordem-servico', 'email-marketing',
            'marketing-multinivel', 'gestao-advocacia', 'corridas-mobilidade', 'rede-social',
            'armazenamento-nuvem', 'pdv-vendas', 'restaurante-mesas', 'pizzaria-delivery',
            'central-suporte', 'site-institucional-cms', 'gestao-arquivos-clientes', 'sistema-clinica',
            'controle-empresarial-nfe', 'faturamento-hospedagem', 'marketplace-leiloes', 'loja-virtual',
        ])->delete();
    }
};
