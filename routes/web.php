<?php

use App\Http\Controllers\TbrExportController;
use App\Livewire\Home;
use App\Livewire\Page\AdminSystems;
use App\Livewire\Page\ClientesAccounts;
use App\Livewire\Page\ClientesClients;
use App\Livewire\Page\ClientesCompanies;
use App\Livewire\Page\ClientesDashboard;
use App\Livewire\Page\ClientesLessonLogs;
use App\Livewire\Page\ClientesPlans;
use App\Livewire\Page\ClientesReceitas;
use App\Livewire\Page\Dashboard;
use App\Livewire\Page\FinanceiroAccountTypes;
use App\Livewire\Page\FinanceiroCategories;
use App\Livewire\Page\FinanceiroDashboard;
use App\Livewire\Page\FinanceiroTransactions;
use App\Livewire\Page\SlideShow;
use App\Livewire\Page\TbrCategories;
use App\Livewire\Page\TbrDashboard;
use App\Livewire\Page\TbrDpMissions;
use App\Livewire\Page\TbrEventDetail;
use App\Livewire\Page\TbrEditScores;
use App\Livewire\Page\TbrEventTeams;
use App\Livewire\Page\TbrLinks;
use App\Livewire\Page\TbrModalities;
use App\Livewire\Page\TbrQuestions;
use App\Livewire\Page\TbrRanking;
use App\Livewire\Page\TbrScore;
use App\Livewire\Page\AdminUsers;
use App\Livewire\Page\PublicProjects;
use App\Livewire\Page\PublicProjectDetail;

use App\Livewire\Page\EmpregoDashboard;
use App\Livewire\Page\EmpregoEmpresas;
use App\Livewire\Page\EmpregoVagas;
use App\Livewire\Page\EscolaDashboard;
use App\Livewire\Page\EscolaTurmas;
use App\Livewire\Page\EscolaAlunos;
use App\Livewire\Page\OrdemServicoDashboard;
use App\Livewire\Page\OrdemServicoClientes;
use App\Livewire\Page\OrdemServicoOrdens;
use App\Livewire\Page\EmailMktListas;
use App\Livewire\Page\EmailMktCampanhas;
use App\Livewire\Page\MmnDashboard;
use App\Livewire\Page\MmnMembros;
use App\Livewire\Page\MmnPagamentos;
use App\Livewire\Page\AdvocaciaDashboard;
use App\Livewire\Page\AdvocaciaClientes;
use App\Livewire\Page\AdvocaciaProcessos;
use App\Livewire\Page\MobilidadeDashboard;
use App\Livewire\Page\MobilidadeMotoristas;
use App\Livewire\Page\MobilidadeCorridas;
use App\Livewire\Page\SocialDashboard;
use App\Livewire\Page\SocialPublicacoes;
use App\Livewire\Page\SocialGrupos;
use App\Livewire\Page\NuvemDashboard;
use App\Livewire\Page\NuvemPastas;
use App\Livewire\Page\NuvemArquivos;
use App\Livewire\Page\PdvDashboard;
use App\Livewire\Page\PdvProdutos;
use App\Livewire\Page\PdvVendas;
use App\Livewire\Page\RestauranteDashboard;
use App\Livewire\Page\RestauranteMesas;
use App\Livewire\Page\RestaurantePedidos;
use App\Livewire\Page\PizzariaDashboard;
use App\Livewire\Page\PizzariaProdutos;
use App\Livewire\Page\PizzariaPedidos;
use App\Livewire\Page\SuporteDashboard;
use App\Livewire\Page\SuporteDepartamentos;
use App\Livewire\Page\SuporteTickets;
use App\Livewire\Page\CmsDashboard;
use App\Livewire\Page\CmsPaginas;
use App\Livewire\Page\CmsLeads;
use App\Livewire\Page\ArquivosClientes;
use App\Livewire\Page\ClinicaDashboard;
use App\Livewire\Page\ClinicaPacientes;
use App\Livewire\Page\ClinicaConsultas;
use App\Livewire\Page\ErpDashboard;
use App\Livewire\Page\ErpProdutos;
use App\Livewire\Page\ErpVendas;
use App\Livewire\Page\HostingClientes;
use App\Livewire\Page\HostingFaturas;
use App\Livewire\Page\MarketplaceDashboard;
use App\Livewire\Page\MarketplaceAnuncios;
use App\Livewire\Page\MarketplaceLances;
use App\Livewire\Page\LojaDashboard;
use App\Livewire\Page\LojaProdutos;
use App\Livewire\Page\LojaPedidos;

use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('web.home');

Route::prefix('projetos')->name('projetos.')->group(function () {
    Route::get('/', PublicProjects::class)->name('index');
    Route::get('/{slug}', PublicProjectDetail::class)->name('show');
});

Route::prefix('tbr')->name('tbr.')->group(function () {
    Route::get('/score/{event_id}/{category_id}/{modality_id}', TbrScore::class)->name('score');
    Route::get('/ranking/{event_id}', TbrRanking::class)->name('ranking');
    Route::get('/ranking/{event_id}/slides', SlideShow::class)->name('slide');
    Route::get('/links/{event_id}', TbrLinks::class)->name('link');

    Route::controller(TbrExportController::class)->prefix('/ranking')->name('ranking.')->group(function () {
        Route::get('/{event_id}/pdf', 'pdf')->name('pdf');
        Route::get('/{event_id}/scores-pdf', 'scoresPdf')->name('scoresPdf');
        Route::get('/{event_id}/team-pdf/{team_id}', 'teamPdf')->name('teamPdf');
        Route::get('/{event_id}/team-modality-pdf/{team_id}/{modality}', 'teamModalityPdf')->name('teamModalityPdf');
        Route::get('/{event_id}/scores-pdf-filtered', 'scoresPdfFiltered')->name('scoresPdfFiltered');
    });
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->middleware('system.access')->group(function () {

    Route::prefix('dashboard')->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');

        Route::get('/tbr', TbrDashboard::class)->name('tbr.dashboard');
        Route::get('/tbr/categorias', TbrCategories::class)->name('tbr.categories');
        Route::get('/tbr/perguntas', TbrQuestions::class)->name('tbr.questions');
        Route::get('/tbr/modalidades', TbrModalities::class)->name('tbr.modalities');
        Route::get('/tbr/missoes-dp', TbrDpMissions::class)->name('tbr.dp-missions');
        Route::get('/tbr/equipes/{event_id}', TbrEventTeams::class)->name('tbr.event-teams');
        Route::get('/tbr/equipes/{event_id}/editar-notas', TbrEditScores::class)->name('tbr.edit-scores');
        Route::get('/tbr/evento/{event_id}', TbrEventDetail::class)->name('tbr.event-detail');

        // Financeiro
        Route::get('/financeiro', FinanceiroDashboard::class)->name('financeiro.dashboard');
        Route::get('/financeiro/contas', FinanceiroTransactions::class)->name('financeiro.transactions');
        Route::get('/financeiro/tipos-conta', FinanceiroAccountTypes::class)->name('financeiro.account-types');
        Route::get('/financeiro/categorias', FinanceiroCategories::class)->name('financeiro.categories');

        // Clientes
        Route::get('/clientes', ClientesDashboard::class)->name('clientes.dashboard');
        Route::get('/clientes/clientes', ClientesClients::class)->name('clientes.clients');
        Route::get('/clientes/planos', ClientesPlans::class)->name('clientes.plans');
        Route::get('/clientes/receitas', ClientesReceitas::class)->name('clientes.receitas');
        Route::get('/clientes/gastos', ClientesAccounts::class)->name('clientes.accounts');
        Route::get('/clientes/aulas', ClientesLessonLogs::class)->name('clientes.lesson-logs');
        Route::get('/clientes/empresas', ClientesCompanies::class)->name('clientes.companies');

        // Vagas de Emprego
        Route::get('/vagas-emprego', EmpregoDashboard::class)->name('vagas-emprego.dashboard');
        Route::get('/vagas-emprego/empresas', EmpregoEmpresas::class)->name('vagas-emprego.empresas');
        Route::get('/vagas-emprego/vagas', EmpregoVagas::class)->name('vagas-emprego.vagas');

        // Gestão Escolar
        Route::get('/gestao-escolar', EscolaDashboard::class)->name('gestao-escolar.dashboard');
        Route::get('/gestao-escolar/turmas', EscolaTurmas::class)->name('gestao-escolar.turmas');
        Route::get('/gestao-escolar/alunos', EscolaAlunos::class)->name('gestao-escolar.alunos');

        // Ordem de Serviço
        Route::get('/ordem-servico', OrdemServicoDashboard::class)->name('ordem-servico.dashboard');
        Route::get('/ordem-servico/clientes', OrdemServicoClientes::class)->name('ordem-servico.clientes');
        Route::get('/ordem-servico/ordens', OrdemServicoOrdens::class)->name('ordem-servico.ordens');

        // Email Marketing (incorporado ao Marketing Multinível — um sistema só)
        Route::get('/email-marketing/listas', EmailMktListas::class)->name('email-marketing.listas');
        Route::get('/email-marketing/campanhas', EmailMktCampanhas::class)->name('email-marketing.campanhas');

        // Marketing Multinível
        Route::get('/marketing-multinivel', MmnDashboard::class)->name('marketing-multinivel.dashboard');
        Route::get('/marketing-multinivel/membros', MmnMembros::class)->name('marketing-multinivel.membros');
        Route::get('/marketing-multinivel/pagamentos', MmnPagamentos::class)->name('marketing-multinivel.pagamentos');

        // Gestão Advocacia
        Route::get('/gestao-advocacia', AdvocaciaDashboard::class)->name('gestao-advocacia.dashboard');
        Route::get('/gestao-advocacia/clientes', AdvocaciaClientes::class)->name('gestao-advocacia.clientes');
        Route::get('/gestao-advocacia/processos', AdvocaciaProcessos::class)->name('gestao-advocacia.processos');

        // Corridas / Mobilidade
        Route::get('/corridas-mobilidade', MobilidadeDashboard::class)->name('corridas-mobilidade.dashboard');
        Route::get('/corridas-mobilidade/motoristas', MobilidadeMotoristas::class)->name('corridas-mobilidade.motoristas');
        Route::get('/corridas-mobilidade/corridas', MobilidadeCorridas::class)->name('corridas-mobilidade.corridas');

        // Rede Social
        Route::get('/rede-social', SocialDashboard::class)->name('rede-social.dashboard');
        Route::get('/rede-social/publicacoes', SocialPublicacoes::class)->name('rede-social.publicacoes');
        Route::get('/rede-social/grupos', SocialGrupos::class)->name('rede-social.grupos');

        // Armazenamento em Nuvem
        Route::get('/armazenamento-nuvem', NuvemDashboard::class)->name('armazenamento-nuvem.dashboard');
        Route::get('/armazenamento-nuvem/pastas', NuvemPastas::class)->name('armazenamento-nuvem.pastas');
        Route::get('/armazenamento-nuvem/arquivos', NuvemArquivos::class)->name('armazenamento-nuvem.arquivos');
        Route::get('/armazenamento-nuvem/clientes', ArquivosClientes::class)->name('armazenamento-nuvem.clientes');

        // PDV / Vendas
        Route::get('/pdv-vendas', PdvDashboard::class)->name('pdv-vendas.dashboard');
        Route::get('/pdv-vendas/produtos', PdvProdutos::class)->name('pdv-vendas.produtos');
        Route::get('/pdv-vendas/vendas', PdvVendas::class)->name('pdv-vendas.vendas');

        // Restaurante — Mesas
        Route::get('/restaurante-mesas', RestauranteDashboard::class)->name('restaurante-mesas.dashboard');
        Route::get('/restaurante-mesas/mesas', RestauranteMesas::class)->name('restaurante-mesas.mesas');
        Route::get('/restaurante-mesas/pedidos', RestaurantePedidos::class)->name('restaurante-mesas.pedidos');

        // Pizzaria Delivery
        Route::get('/pizzaria-delivery', PizzariaDashboard::class)->name('pizzaria-delivery.dashboard');
        Route::get('/pizzaria-delivery/produtos', PizzariaProdutos::class)->name('pizzaria-delivery.produtos');
        Route::get('/pizzaria-delivery/pedidos', PizzariaPedidos::class)->name('pizzaria-delivery.pedidos');

        // Central de Suporte
        Route::get('/central-suporte', SuporteDashboard::class)->name('central-suporte.dashboard');
        Route::get('/central-suporte/departamentos', SuporteDepartamentos::class)->name('central-suporte.departamentos');
        Route::get('/central-suporte/tickets', SuporteTickets::class)->name('central-suporte.tickets');

        // Site Institucional / CMS
        Route::get('/site-institucional-cms', CmsDashboard::class)->name('site-institucional-cms.dashboard');
        Route::get('/site-institucional-cms/paginas', CmsPaginas::class)->name('site-institucional-cms.paginas');
        Route::get('/site-institucional-cms/leads', CmsLeads::class)->name('site-institucional-cms.leads');

        // Sistema para Clínicas
        Route::get('/sistema-clinica', ClinicaDashboard::class)->name('sistema-clinica.dashboard');
        Route::get('/sistema-clinica/pacientes', ClinicaPacientes::class)->name('sistema-clinica.pacientes');
        Route::get('/sistema-clinica/consultas', ClinicaConsultas::class)->name('sistema-clinica.consultas');

        // Controle Empresarial (RH + NFe)
        Route::get('/controle-empresarial-nfe', ErpDashboard::class)->name('controle-empresarial-nfe.dashboard');
        Route::get('/controle-empresarial-nfe/produtos', ErpProdutos::class)->name('controle-empresarial-nfe.produtos');
        Route::get('/controle-empresarial-nfe/vendas', ErpVendas::class)->name('controle-empresarial-nfe.vendas');

        // Faturamento de Hospedagem (incorporado ao Financeiro — um sistema só)
        Route::get('/faturamento-hospedagem/clientes', HostingClientes::class)->name('faturamento-hospedagem.clientes');
        Route::get('/faturamento-hospedagem/faturas', HostingFaturas::class)->name('faturamento-hospedagem.faturas');

        // Marketplace com Leilões
        Route::get('/marketplace-leiloes', MarketplaceDashboard::class)->name('marketplace-leiloes.dashboard');
        Route::get('/marketplace-leiloes/anuncios', MarketplaceAnuncios::class)->name('marketplace-leiloes.anuncios');
        Route::get('/marketplace-leiloes/lances', MarketplaceLances::class)->name('marketplace-leiloes.lances');

        // Loja Virtual
        Route::get('/loja-virtual', LojaDashboard::class)->name('loja-virtual.dashboard');
        Route::get('/loja-virtual/produtos', LojaProdutos::class)->name('loja-virtual.produtos');
        Route::get('/loja-virtual/pedidos', LojaPedidos::class)->name('loja-virtual.pedidos');

        // Admin
        Route::get('/admin/sistemas', AdminSystems::class)->name('admin.systems')
            ->middleware('can:delete');
        Route::get('/admin/usuarios', AdminUsers::class)->name('admin.users')
            ->middleware('can:manage-users');
    });
});

// Accept invitation - needs auth but NOT system.access (user may not have system_id yet)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/clientes/empresas/convite/{invitation}/aceitar', function (App\Models\TeamInvitation $invitation) {
    $user = auth()->user();

    if (strtolower($user->email) !== strtolower($invitation->email)) {
        abort(403, 'Este convite não é para você.');
    }

    if ($invitation->company->users()->where('user_id', $user->id)->exists()) {
        $invitation->delete();
        return redirect()->route('clientes.companies')
            ->with('flash.banner', 'Você já faz parte desta empresa.')
            ->with('flash.bannerStyle', 'success');
    }

    $invitation->company->users()->attach($user->id, ['role' => $invitation->role ?? 'user']);

    if (!$user->system_id && $invitation->company->system_id) {
        $user->system_id = $invitation->company->system_id;
        $user->save();
    }

    $invitation->delete();

    return redirect()->route('clientes.companies')
        ->with('flash.banner', 'Você entrou para a empresa!')
        ->with('flash.bannerStyle', 'success');
})->name('clientes.companies.accept-invitation');
