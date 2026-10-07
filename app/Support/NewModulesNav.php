<?php

namespace App\Support;

class NewModulesNav
{
    public static function all(): array
    {
        return [
            'vagas-emprego' => ['label' => 'Vagas de Emprego', 'icon' => 'fa-briefcase', 'pages' => [
                ['route' => 'vagas-emprego.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'vagas-emprego.empresas', 'label' => 'Empresas', 'icon' => 'fa-building'],
                ['route' => 'vagas-emprego.vagas', 'label' => 'Vagas', 'icon' => 'fa-list'],
            ]],
            'gestao-escolar' => ['label' => 'Gestão Escolar', 'icon' => 'fa-graduation-cap', 'pages' => [
                ['route' => 'gestao-escolar.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'gestao-escolar.turmas', 'label' => 'Turmas', 'icon' => 'fa-chalkboard'],
                ['route' => 'gestao-escolar.alunos', 'label' => 'Alunos', 'icon' => 'fa-user-graduate'],
            ]],
            'ordem-servico' => ['label' => 'Ordem de Serviço', 'icon' => 'fa-screwdriver-wrench', 'pages' => [
                ['route' => 'ordem-servico.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'ordem-servico.clientes', 'label' => 'Clientes', 'icon' => 'fa-users'],
                ['route' => 'ordem-servico.ordens', 'label' => 'Ordens', 'icon' => 'fa-clipboard-list'],
            ]],
            'marketing-multinivel' => ['label' => 'Marketing Multinível', 'icon' => 'fa-sitemap', 'pages' => [
                ['route' => 'marketing-multinivel.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'marketing-multinivel.membros', 'label' => 'Membros', 'icon' => 'fa-users'],
                ['route' => 'marketing-multinivel.pagamentos', 'label' => 'Pagamentos', 'icon' => 'fa-money-bill-wave'],
                ['route' => 'email-marketing.listas', 'label' => 'Listas de Email', 'icon' => 'fa-list'],
                ['route' => 'email-marketing.campanhas', 'label' => 'Campanhas de Email', 'icon' => 'fa-paper-plane'],
            ]],
            'gestao-advocacia' => ['label' => 'Advocacia', 'icon' => 'fa-scale-balanced', 'pages' => [
                ['route' => 'gestao-advocacia.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'gestao-advocacia.clientes', 'label' => 'Clientes', 'icon' => 'fa-users'],
                ['route' => 'gestao-advocacia.processos', 'label' => 'Processos', 'icon' => 'fa-folder-open'],
            ]],
            'corridas-mobilidade' => ['label' => 'Mobilidade', 'icon' => 'fa-car-side', 'pages' => [
                ['route' => 'corridas-mobilidade.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'corridas-mobilidade.motoristas', 'label' => 'Motoristas', 'icon' => 'fa-id-card'],
                ['route' => 'corridas-mobilidade.corridas', 'label' => 'Corridas', 'icon' => 'fa-route'],
            ]],
            'rede-social' => ['label' => 'Rede Social', 'icon' => 'fa-users', 'pages' => [
                ['route' => 'rede-social.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'rede-social.publicacoes', 'label' => 'Publicações', 'icon' => 'fa-comment'],
                ['route' => 'rede-social.grupos', 'label' => 'Grupos', 'icon' => 'fa-people-group'],
            ]],
            'armazenamento-nuvem' => ['label' => 'Armaz. em Nuvem', 'icon' => 'fa-cloud-arrow-up', 'pages' => [
                ['route' => 'armazenamento-nuvem.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'armazenamento-nuvem.pastas', 'label' => 'Pastas', 'icon' => 'fa-folder'],
                ['route' => 'armazenamento-nuvem.arquivos', 'label' => 'Arquivos', 'icon' => 'fa-file'],
                ['route' => 'armazenamento-nuvem.clientes', 'label' => 'Clientes', 'icon' => 'fa-users'],
            ]],
            'pdv-vendas' => ['label' => 'PDV / Vendas', 'icon' => 'fa-cash-register', 'pages' => [
                ['route' => 'pdv-vendas.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'pdv-vendas.produtos', 'label' => 'Produtos', 'icon' => 'fa-box'],
                ['route' => 'pdv-vendas.vendas', 'label' => 'Vendas', 'icon' => 'fa-receipt'],
            ]],
            'restaurante-mesas' => ['label' => 'Restaurante', 'icon' => 'fa-utensils', 'pages' => [
                ['route' => 'restaurante-mesas.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'restaurante-mesas.mesas', 'label' => 'Mesas', 'icon' => 'fa-chair'],
                ['route' => 'restaurante-mesas.pedidos', 'label' => 'Pedidos', 'icon' => 'fa-receipt'],
            ]],
            'pizzaria-delivery' => ['label' => 'Pizzaria', 'icon' => 'fa-pizza-slice', 'pages' => [
                ['route' => 'pizzaria-delivery.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'pizzaria-delivery.produtos', 'label' => 'Produtos', 'icon' => 'fa-box'],
                ['route' => 'pizzaria-delivery.pedidos', 'label' => 'Pedidos', 'icon' => 'fa-receipt'],
            ]],
            'central-suporte' => ['label' => 'Suporte', 'icon' => 'fa-headset', 'pages' => [
                ['route' => 'central-suporte.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'central-suporte.departamentos', 'label' => 'Departamentos', 'icon' => 'fa-building'],
                ['route' => 'central-suporte.tickets', 'label' => 'Tickets', 'icon' => 'fa-ticket'],
            ]],
            'site-institucional-cms' => ['label' => 'Site Institucional', 'icon' => 'fa-building-columns', 'pages' => [
                ['route' => 'site-institucional-cms.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'site-institucional-cms.paginas', 'label' => 'Páginas', 'icon' => 'fa-file-lines'],
                ['route' => 'site-institucional-cms.leads', 'label' => 'Leads', 'icon' => 'fa-address-card'],
            ]],
            'sistema-clinica' => ['label' => 'Clínica', 'icon' => 'fa-stethoscope', 'pages' => [
                ['route' => 'sistema-clinica.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'sistema-clinica.pacientes', 'label' => 'Pacientes', 'icon' => 'fa-users'],
                ['route' => 'sistema-clinica.consultas', 'label' => 'Consultas', 'icon' => 'fa-calendar-check'],
            ]],
            'controle-empresarial-nfe' => ['label' => 'Controle Empresarial', 'icon' => 'fa-building', 'pages' => [
                ['route' => 'controle-empresarial-nfe.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'controle-empresarial-nfe.produtos', 'label' => 'Produtos', 'icon' => 'fa-box'],
                ['route' => 'controle-empresarial-nfe.vendas', 'label' => 'Vendas / NFe', 'icon' => 'fa-file-invoice'],
            ]],
            'marketplace-leiloes' => ['label' => 'Marketplace', 'icon' => 'fa-gavel', 'pages' => [
                ['route' => 'marketplace-leiloes.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'marketplace-leiloes.anuncios', 'label' => 'Anúncios', 'icon' => 'fa-bullhorn'],
                ['route' => 'marketplace-leiloes.lances', 'label' => 'Lances', 'icon' => 'fa-hand-holding-dollar'],
            ]],
            'loja-virtual' => ['label' => 'Loja Virtual', 'icon' => 'fa-store', 'pages' => [
                ['route' => 'loja-virtual.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'],
                ['route' => 'loja-virtual.produtos', 'label' => 'Produtos', 'icon' => 'fa-box'],
                ['route' => 'loja-virtual.pedidos', 'label' => 'Pedidos', 'icon' => 'fa-receipt'],
            ]],
        ];
    }

    /**
     * Lista plana de todos os 23 sistemas (os 3 originais + os 20 módulos),
     * cada um com slug/label/icon/route do dashboard — usada em menus que
     * precisam listar/linkar todos os sistemas de uma vez.
     */
    public static function allSystems(): array
    {
        $core = [
            ['slug' => 'tbr', 'label' => 'TBR', 'icon' => 'fa-trophy', 'route' => 'tbr.dashboard'],
            ['slug' => 'financeiro', 'label' => 'Financeiro', 'icon' => 'fa-wallet', 'route' => 'financeiro.dashboard'],
            ['slug' => 'clientes', 'label' => 'Clientes', 'icon' => 'fa-user-tie', 'route' => 'clientes.dashboard'],
        ];

        $modules = array_map(
            fn($slug, $module) => [
                'slug' => $slug,
                'label' => $module['label'],
                'icon' => $module['icon'],
                'route' => $module['pages'][0]['route'],
            ],
            array_keys(static::all()),
            static::all()
        );

        return array_merge($core, $modules);
    }
}
