# Ideias.dev.br

Plataforma multi-sistema da Ideias Dev: um único projeto Laravel que hospeda **20 sistemas de gestão independentes**, cada um com seu próprio conjunto de rotas, tabelas e telas, além de uma vitrine pública que apresenta e vende esses sistemas.

> **Nota:** a plataforma já teve 23 sistemas. Três foram **fundidos** em outros (migração `2026_07_04_000002_retire_merged_systems.php`): `email-marketing` → `marketing-multinivel`, `gestao-arquivos-clientes` → `armazenamento-nuvem` e `faturamento-hospedagem` → `financeiro`. As telas continuam existindo, mas como páginas internas do sistema que os absorveu.

## Stack

- **Laravel 12** (PHP)
- **Livewire** — cada tela é um componente full-page (sem controllers tradicionais)
- **Jetstream** — autenticação, times, perfil
- **Tailwind CSS 3** + Font Awesome
- **Chart.js** — gráficos dos dashboards
- Banco padrão local: SQLite (`DB_CONNECTION=sqlite`), configurável para MySQL em produção

## Como rodar localmente

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev        # build dos assets
php artisan serve  # http://127.0.0.1:8000
```

## Conceito: um projeto, vários sistemas

Cada usuário pertence a **um sistema** (coluna `system_id` em `users`, tabela `systems`). Ao logar, o usuário comum é redirecionado direto para o dashboard do seu sistema; o **super admin** (`role_id = 1`) enxerga e acessa todos.

Peças-chave desse isolamento:

| Arquivo | Papel |
|---|---|
| `database/migrations/2025_06_18_000002_create_systems_table.php` | Tabela `systems` (catálogo de sistemas) + coluna `system_id` em `users` |
| `app/Http/Middleware/CheckSystemAccess.php` | Bloqueia o usuário de acessar `/dashboard/{slug}` de um sistema que não é o seu (super admin passa livre) |
| `app/Livewire/Page/Dashboard.php` | `/dashboard` — redireciona usuário comum pro dashboard do seu sistema; para super admin, mostra visão geral de tudo |
| `resources/views/partials/sidebar-nav.blade.php` + `app/Support/NewModulesNav.php` | Menu lateral dinâmico: mostra só as páginas do sistema em que o usuário está navegando |

Todas as telas de cada sistema seguem o mesmo padrão: **Dashboard** (estatísticas) + páginas de **CRUD** (lista, modal criar/editar, excluir com confirmação), com dados isolados por `user_id`/`system_id`.

## Vitrine pública (`/projetos`)

Não exige login. Mostra os projetos cadastrados como cards (busca + filtro por categoria) e uma página de detalhe por sistema com descrição, "como funciona" (passo a passo) e botão "Acessar sistema".

| Rota | Componente |
|---|---|
| `/projetos` | `App\Livewire\Page\PublicProjects` |
| `/projetos/{slug}` | `App\Livewire\Page\PublicProjectDetail` |

Dados na tabela `projects` (`app/Models/Project.php`, populada por `database/seeders/ProjectSeeder.php` — hoje são **17 projetos ativos**: os 3 sistemas originais, TBR, Financeiro e Clientes, não têm card na vitrine). A home (`/`) também mostra os 4 projetos mais recentes com um botão "Ver mais projetos".

## `/dashboard` — visão geral (super admin)

Para o super admin (usuário sem `system_id` fixo), o `/dashboard` mostra um **grid com busca** de todos os 20 sistemas, cada um com um contador rápido (ex: "12 vagas", "8 tickets") e link direto pro respectivo dashboard — uma tela só no lugar de uma aba por sistema.

Para o usuário comum, o mesmo `/dashboard` redireciona direto pro dashboard do sistema dele (o mapa de redirecionamento está em `app/Livewire/Page/Dashboard.php::mount()`).

---

## Os 20 sistemas

### 1. TBR — Gestão de Eventos (`tbr`)

Sistema original da plataforma: gestão de eventos esportivos/competições, equipes, categorias, ranking e pontuação.

- Rotas autenticadas: `/dashboard/tbr`, `/tbr/categorias`, `/tbr/perguntas`, `/tbr/modalidades`, `/tbr/missoes-dp`, `/tbr/equipes/{event_id}`, `/tbr/equipes/{event_id}/editar-notas`, `/tbr/evento/{event_id}`
- Rotas públicas (sem login): `/tbr/score/{...}`, `/tbr/ranking/{event_id}`, `/tbr/ranking/{event_id}/slides`, `/tbr/links/{event_id}` + exportações em PDF (`/tbr/ranking/{event_id}/pdf`, `scores-pdf`, `team-pdf`, etc. via `App\Http\Controllers\TbrExportController`)
- Tabelas principais: `events`, `teams`, `categories`, `modalities`, `dp_missions`, `assessment_questions`, `team_modality_scores`
- Models: `Event`, `Team`, `Category`, `Modality`, `DpMission`, `AssessmentQuestion`, `TeamModalityScore`

### 2. Financeiro — Controle Financeiro (`financeiro`)

Controle de receitas/despesas pessoais ou de empresa: lançamentos, categorias e tipos de conta, com tendência mensal. **Absorveu o antigo "Faturamento de Hospedagem"**: clientes de hospedagem e faturas são páginas deste sistema (rota `/dashboard/faturamento-hospedagem/...`, links na mesma sidebar).

- Rotas: `/dashboard/financeiro`, `/financeiro/contas`, `/financeiro/tipos-conta`, `/financeiro/categorias` + páginas de hospedagem: `/dashboard/faturamento-hospedagem/clientes` e `/dashboard/faturamento-hospedagem/faturas`
- Tabelas: `financial_transactions`, `financial_categories`, `account_types`, `hosting_clients`, `hosting_invoices`
- Models: `FinancialTransaction`, `FinancialCategory`, `AccountType`, `HostingCliente`, `HostingFatura`

### 3. Clientes — Controle de Clientes e Planos (`clientes`)

Gestão de clientes, planos contratados, empresas, contas a receber/pagar e histórico de aulas/atendimentos.

- Rotas: `/dashboard/clientes...` (dashboard, clientes, planos, contas, empresas, registros de aula)
- Tabelas: `clients`, `plans`, `client_plans`, `client_accounts`, `companies`, `lesson_logs`
- Models: `Client`, `Plan`, `ClientPlan`, `ClientAccount`, `Company`, `LessonLog`

### 4. Vagas de Emprego (`vagas-emprego`)

Portal de vagas: empresas cadastram vagas, candidatos se aplicam.

- Rotas: `/dashboard/vagas-emprego`, `/empresas`, `/vagas`
- Tabelas: `emprego_companies`, `emprego_jobs`, `emprego_job_seekers`, `emprego_applications`
- Models: `EmpregoCompany`, `EmpregoJob`, `EmpregoJobSeeker`, `EmpregoApplication`

### 5. Gestão Escolar / EAD (`gestao-escolar`)

Turmas, alunos e faturas de mensalidade.

- Rotas: `/dashboard/gestao-escolar`, `/turmas`, `/alunos`
- Tabelas: `escola_classes`, `escola_students`, `escola_invoices`
- Models: `EscolaTurma`, `EscolaAluno`, `EscolaFatura`

### 6. Ordem de Serviço (`ordem-servico`)

Abertura de ordens de serviço para clientes, com lançamentos financeiros vinculados.

- Rotas: `/dashboard/ordem-servico`, `/clientes`, `/ordens`
- Tabelas: `os_customers`, `os_service_orders`, `os_financial_entries`
- Models: `OrdemServicoCliente`, `OrdemServicoOrdem`, `OrdemServicoLancamento`

### 7. Marketing Multinível — MMN (`marketing-multinivel`)

Rede de membros com patrocinador/downline, níveis e pagamentos. **Absorveu o antigo "Email Marketing"**: listas de contatos e campanhas de e-mail são páginas deste sistema (rotas `/dashboard/email-marketing/listas` e `/dashboard/email-marketing/campanhas`, sem dashboard próprio).

- Rotas: `/dashboard/marketing-multinivel`, `/marketing-multinivel/membros`, `/marketing-multinivel/pagamentos`, `/dashboard/email-marketing/listas`, `/dashboard/email-marketing/campanhas`
- Tabelas: `mmn_members`, `mmn_payments`, `email_lists`, `email_subscribers`, `email_campaigns`
- Models: `MmnMembro` (com relação de patrocinador/downline), `MmnPagamento`, `EmailMktLista`, `EmailMktAssinante`, `EmailMktCampanha`

### 8. Sistema para Advocacia (`gestao-advocacia`)

Clientes e processos jurídicos (vara, fase, audiência, honorários).

- Rotas: `/dashboard/gestao-advocacia`, `/clientes`, `/processos`
- Tabelas: `advocacia_clients`, `advocacia_cases`
- Models: `AdvocaciaCliente`, `AdvocaciaProcesso`

### 9. Corridas / Mobilidade (`corridas-mobilidade`)

Motoristas e corridas ao estilo Uber/99 (origem, destino, distância, valor).

- Rotas: `/dashboard/corridas-mobilidade`, `/motoristas`, `/corridas`
- Tabelas: `mobi_drivers`, `mobi_rides`
- Models: `MobilidadeMotorista`, `MobilidadeCorrida`

### 10. Rede Social (`rede-social`)

Publicações e grupos, com controle de visibilidade/privacidade.

- Rotas: `/dashboard/rede-social`, `/publicacoes`, `/grupos`
- Tabelas: `social_posts`, `social_groups`
- Models: `SocialPublicacao`, `SocialGrupo`

### 11. Armazenamento em Nuvem (`armazenamento-nuvem`)

Pastas (com subpastas) e arquivos, com opção de compartilhamento público via token. **Absorveu o antigo "Portal de Arquivos com Clientes"**: vincular arquivos a um cliente (com contagem de downloads) virou a página "Clientes" deste sistema, e os dados de `arquivos_files` foram migrados para `nuvem_files` (migração `2026_07_04_000001_merge_arquivos_into_nuvem.php`).

- Rotas: `/dashboard/armazenamento-nuvem`, `/armazenamento-nuvem/pastas`, `/armazenamento-nuvem/arquivos`, `/armazenamento-nuvem/clientes`
- Tabelas: `nuvem_folders`, `nuvem_files`, `arquivos_clients`
- Models: `NuvemPasta`, `NuvemArquivo`, `ArquivosCliente`

### 12. Vendas / PDV (`pdv-vendas`)

Produtos com custo/preço/estoque e registro de vendas.

- Rotas: `/dashboard/pdv-vendas`, `/produtos`, `/vendas`
- Tabelas: `pdv_products`, `pdv_sales`
- Models: `PdvProduto`, `PdvVenda`

### 13. Restaurante — Controle de Mesas (`restaurante-mesas`)

Mesas (status livre/ocupada) e pedidos por mesa.

- Rotas: `/dashboard/restaurante-mesas`, `/mesas`, `/pedidos`
- Tabelas: `mesa_tables`, `mesa_orders`
- Models: `RestauranteMesa`, `RestaurantePedido`

### 14. Pizzaria Delivery (`pizzaria-delivery`)

Produtos e pedidos com endereço de entrega e status.

- Rotas: `/dashboard/pizzaria-delivery`, `/produtos`, `/pedidos`
- Tabelas: `pizza_products`, `pizza_orders`
- Models: `PizzariaProduto`, `PizzariaPedido`

### 15. Central de Suporte / Tickets (`central-suporte`)

Departamentos e tickets com prioridade e status.

- Rotas: `/dashboard/central-suporte`, `/departamentos`, `/tickets`
- Tabelas: `suporte_departments`, `suporte_tickets`
- Models: `SuporteDepartamento`, `SuporteTicket`

### 16. Site Institucional / CMS (`site-institucional-cms`)

Páginas de conteúdo publicáveis e captura de leads.

- Rotas: `/dashboard/site-institucional-cms`, `/paginas`, `/leads`
- Tabelas: `cms_pages`, `cms_leads`
- Models: `CmsPagina`, `CmsLead`

### 17. Sistema para Clínicas (`sistema-clinica`)

Pacientes e agendamento de consultas por médico.

- Rotas: `/dashboard/sistema-clinica`, `/pacientes`, `/consultas`
- Tabelas: `clinica_patients`, `clinica_appointments`
- Models: `ClinicaPaciente`, `ClinicaConsulta`

### 18. Controle Empresarial — RH + NFe (`controle-empresarial-nfe`)

Produtos e vendas com emissão de nota fiscal (número da NFe).

- Rotas: `/dashboard/controle-empresarial-nfe`, `/produtos`, `/vendas`
- Tabelas: `erp_products`, `erp_sales`
- Models: `ErpProduto`, `ErpVenda`

### 19. Marketplace com Leilões (`marketplace-leiloes`)

Anúncios (preço fixo ou leilão) e lances dos compradores.

- Rotas: `/dashboard/marketplace-leiloes`, `/anuncios`, `/lances`
- Tabelas: `marketplace_listings`, `marketplace_bids`
- Models: `MarketplaceAnuncio`, `MarketplaceLance`

### 20. Loja Virtual / E-commerce (`loja-virtual`)

Produtos com estoque e pedidos de clientes (baseado no CubeCart).

- Rotas: `/dashboard/loja-virtual`, `/produtos`, `/pedidos`
- Tabelas: `loja_products`, `loja_orders`
- Models: `LojaProduto`, `LojaPedido`

---

## Estrutura de código (padrão a seguir em novos sistemas)

- **Rotas**: tudo em `routes/web.php`, dentro do `Route::prefix('dashboard')` existente (exige login + middleware `system.access`)
- **Componentes**: `app/Livewire/Page/{Prefixo}{Nome}.php` — nome de arquivo plano com prefixo do sistema (ex: `TbrDashboard.php`, `PdvVendas.php`), sem subpastas
- **Views**: `resources/views/livewire/page/{slug}/{pagina}.blade.php` — uma subpasta por sistema (ex: `tbr/dashboard.blade.php`, `pdv-vendas/produtos.blade.php`)
- **Models**: `app/Models/{Prefixo}{Nome}.php`, todas as tabelas de negócio com `user_id` (e/ou `system_id` quando aplicável)
- **Menu lateral**: adicionar entrada em `app/Support/NewModulesNav.php` (label, ícone, páginas) para o sistema aparecer na sidebar
- **Vitrine pública**: adicionar registro em `database/seeders/ProjectSeeder.php` (tabela `projects`) para o sistema aparecer em `/projetos`

Documentação histórica em `.github/`: `CHANGELOG-INTEGRACAO-SISTEMAS.md` e `INTEGRACAO-SISTEMAS-PHP.md` (integração dos 20 módulos a partir de sistemas PHP legados) e `CHANGELOG-FUSAO-E-PADRONIZACAO.md` (fusão de sistemas, padronização de UI e revisão de lógica).
