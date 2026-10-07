# Changelog completo — Integração dos 31 sistemas PHP no IdeiasDev

Documento técnico com **todas** as rotas, tabelas, models, componentes e arquivos alterados nesta integração. Para uma visão resumida de "como testar", veja `INTEGRACAO-SISTEMAS-PHP.md`.

---

## 1. Arquivos existentes que foram MODIFICADOS

| Arquivo | O que mudou |
|---|---|
| `routes/web.php` | Adicionado bloco público `Route::prefix('projetos')` (`projetos.index`, `projetos.show`) antes do grupo `tbr`. Adicionadas ~63 rotas novas dentro do `Route::prefix('dashboard')` existente (uma para cada dashboard + página CRUD dos 20 módulos). Adicionados ~62 `use App\Livewire\Page\...` novos no topo do arquivo. |
| `app/Http/Middleware/CheckSystemAccess.php` | O array `$systemRoutes` ganhou 20 novas entradas `'dashboard/{slug}' => '{slug}'`, uma por módulo, para que o controle de acesso por sistema (usuário só acessa o sistema que lhe foi atribuído) valha também para os módulos novos. |
| `app/Livewire/Page/Dashboard.php` | O array `$redirects` (usado em `mount()` para mandar o usuário comum direto pro dashboard do seu sistema) ganhou 20 novas entradas `'{slug}' => '{slug}.dashboard'`. |
| `resources/views/partials/sidebar-nav.blade.php` | Adicionado bloco `@if ($activeModuleSlug)` no fim da lista de navegação, que percorre `App\Support\NewModulesNav::all()` e imprime os links (Dashboard + páginas CRUD) do módulo em que o usuário está navegando. |
| `resources/views/partials/sidebar.blade.php` | O título do cabeçalho da sidebar (`TBR` / `Clientes` / `Financeiro` / `Menu`) ganhou mais uma condição `@elseif ($activeModuleSlug)` que mostra o nome do módulo novo ativo. |
| `resources/views/navigation-menu-web.blade.php` | Adicionado link "Projetos" no menu público, ao lado de "Home". |
| `database/seeders/DatabaseSeeder.php` | Adicionado `ProjectSeeder::class` na lista de seeders chamados em `run()`. |

## 2. Arquivos novos de infraestrutura (não são um módulo específico)

| Arquivo | Papel |
|---|---|
| `database/migrations/2026_07_03_000001_create_projects_table.php` | Cria a tabela `projects` (vitrine pública). |
| `database/migrations/2026_07_03_000030_seed_new_systems.php` | Insere os 20 novos registros na tabela `systems` (já existente). |
| `app/Models/Project.php` | Model da vitrine pública. |
| `database/seeders/ProjectSeeder.php` | Popula a tabela `projects` com os 20 registros (nome, descrição curta/longa, categoria, ícone, fluxo). |
| `app/Livewire/Page/PublicProjects.php` + `resources/views/livewire/page/public/projects.blade.php` | Página `/projetos` — grid de cards com busca e filtro por categoria. |
| `app/Livewire/Page/PublicProjectDetail.php` + `resources/views/livewire/page/public/project-detail.blade.php` | Página `/projetos/{slug}` — detalhe do projeto, "como funciona" e botão "Acessar sistema". |
| `app/Support/NewModulesNav.php` | Array central com label/ícone/páginas de cada um dos 20 módulos nc — fonte única usada pela sidebar para montar o menu sem duplicar HTML 20 vezes. |

### Tabela `projects` (vitrine pública)

```
id, slug (unique), name, category, icon, short_description,
long_description (text), workflow (json), system_slug, sort_order,
active (bool), timestamps
```

### Rotas da vitrine pública

| Método | URL | Nome da rota | Componente |
|---|---|---|---|
| GET | `/projetos` | `projetos.index` | `App\Livewire\Page\PublicProjects` |
| GET | `/projetos/{slug}` | `projetos.show` | `App\Livewire\Page\PublicProjectDetail` |

---

## 3. Os 20 módulos — rotas, tabelas, models e componentes de cada um

Convenção comum a todos: toda rota fica sob `/dashboard/...` (exige login + `system.access`), toda tabela de negócio tem `user_id` (FK para `users`, cascade), toda página CRUD segue o padrão `showModal / edit($id) / save() / confirmDelete($id) / executeAction() / resetForm()` igual aos módulos `financeiro`/`clientes` já existentes.

### 3.1 `vagas-emprego` — Vagas de Emprego

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/vagas-emprego` | `vagas-emprego.dashboard` | `EmpregoDashboard` |
| `/dashboard/vagas-emprego/empresas` | `vagas-emprego.empresas` | `EmpregoEmpresas` |
| `/dashboard/vagas-emprego/vagas` | `vagas-emprego.vagas` | `EmpregoVagas` |

**Tabelas:**
- `emprego_companies`: user_id, name, contact_name, email, phone, city, state, description, plan (default `free`), active (bool)
- `emprego_jobs`: user_id, company_id → `emprego_companies`, title, category, description, requirements, salary, city, state, expires_at, status (default `aberta`)
- `emprego_job_seekers`: user_id, name, email, phone, city, state, birth_date, summary, skills
- `emprego_applications`: user_id, job_id → `emprego_jobs`, job_seeker_id → `emprego_job_seekers`, status (default `pendente`), applied_at

**Models:** `EmpregoCompany`, `EmpregoJob`, `EmpregoJobSeeker`, `EmpregoApplication`

### 3.2 `gestao-escolar` — Gestão Escolar / EAD

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/gestao-escolar` | `gestao-escolar.dashboard` | `EscolaDashboard` |
| `/dashboard/gestao-escolar/turmas` | `gestao-escolar.turmas` | `EscolaTurmas` |
| `/dashboard/gestao-escolar/alunos` | `gestao-escolar.alunos` | `EscolaAlunos` |

**Tabelas:**
- `escola_classes`: user_id, name, teacher_name, shift
- `escola_students`: user_id, class_id → `escola_classes`, name, birth_date, guardian_name, guardian_phone, email, active (bool)
- `escola_invoices`: user_id, student_id → `escola_students`, title, amount, due_date, paid (bool), paid_date

**Models:** `EscolaTurma`, `EscolaAluno`, `EscolaFatura`

### 3.3 `ordem-servico` — Ordem de Serviço

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/ordem-servico` | `ordem-servico.dashboard` | `OrdemServicoDashboard` |
| `/dashboard/ordem-servico/clientes` | `ordem-servico.clientes` | `OrdemServicoClientes` |
| `/dashboard/ordem-servico/ordens` | `ordem-servico.ordens` | `OrdemServicoOrdens` |

**Tabelas:**
- `os_customers`: user_id, name, document, phone, email, address
- `os_service_orders`: user_id, customer_id → `os_customers`, equipment_description, defect, status (default `aberta`), total_value, start_date, end_date
- `os_financial_entries`: user_id, service_order_id → `os_service_orders`, description, amount, due_date, paid (bool)

**Models:** `OrdemServicoCliente`, `OrdemServicoOrdem`, `OrdemServicoLancamento`

### 3.4 `email-marketing` — Email Marketing

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/email-marketing` | `email-marketing.dashboard` | `EmailMktDashboard` |
| `/dashboard/email-marketing/listas` | `email-marketing.listas` | `EmailMktListas` |
| `/dashboard/email-marketing/campanhas` | `email-marketing.campanhas` | `EmailMktCampanhas` |

**Tabelas:**
- `email_lists`: user_id, name, description
- `email_subscribers`: user_id, list_id → `email_lists`, email, name, confirmed (bool), unsubscribed (bool)
- `email_campaigns`: user_id, list_id → `email_lists` (nullable), subject, body, status (default `rascunho`), sent_at

**Models:** `EmailMktLista`, `EmailMktAssinante`, `EmailMktCampanha`

### 3.5 `marketing-multinivel` — Marketing Multinível (MMN)

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/marketing-multinivel` | `marketing-multinivel.dashboard` | `MmnDashboard` |
| `/dashboard/marketing-multinivel/membros` | `marketing-multinivel.membros` | `MmnMembros` |
| `/dashboard/marketing-multinivel/pagamentos` | `marketing-multinivel.pagamentos` | `MmnPagamentos` |

**Tabelas:**
- `mmn_members`: user_id, name, email, phone, sponsor_id → `mmn_members` (auto-referência), level (int, default 1), balance (default 0), status (default `ativo`)
- `mmn_payments`: user_id, member_id → `mmn_members`, amount, status (default `pendente`), paid_at, proof_note

**Models:** `MmnMembro` (com relação de patrocinador/downline), `MmnPagamento`

### 3.6 `gestao-advocacia` — Sistema para Advocacia

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/gestao-advocacia` | `gestao-advocacia.dashboard` | `AdvocaciaDashboard` |
| `/dashboard/gestao-advocacia/clientes` | `gestao-advocacia.clientes` | `AdvocaciaClientes` |
| `/dashboard/gestao-advocacia/processos` | `gestao-advocacia.processos` | `AdvocaciaProcessos` |

**Tabelas:**
- `advocacia_clients`: user_id, name, phone, email, address
- `advocacia_cases`: user_id, client_id → `advocacia_clients`, title, case_no, court, stage (default `inicial`), hearing_date, fees

**Models:** `AdvocaciaCliente`, `AdvocaciaProcesso`

### 3.7 `corridas-mobilidade` — Corridas / Mobilidade

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/corridas-mobilidade` | `corridas-mobilidade.dashboard` | `MobilidadeDashboard` |
| `/dashboard/corridas-mobilidade/motoristas` | `corridas-mobilidade.motoristas` | `MobilidadeMotoristas` |
| `/dashboard/corridas-mobilidade/corridas` | `corridas-mobilidade.corridas` | `MobilidadeCorridas` |

**Tabelas:**
- `mobi_drivers`: user_id, name, phone, license_no, vehicle_category (default `sedan`), status (default `ativo`)
- `mobi_rides`: user_id, driver_id → `mobi_drivers` (nullable), rider_name, pickup_address, drop_address, status (default `pendente`), distance_km, amount, requested_at

**Models:** `MobilidadeMotorista`, `MobilidadeCorrida`

### 3.8 `rede-social` — Rede Social

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/rede-social` | `rede-social.dashboard` | `SocialDashboard` |
| `/dashboard/rede-social/publicacoes` | `rede-social.publicacoes` | `SocialPublicacoes` |
| `/dashboard/rede-social/grupos` | `rede-social.grupos` | `SocialGrupos` |

**Tabelas:**
- `social_posts`: user_id, content (text), media_url, visibility (default `publico`)
- `social_groups`: user_id, name, description, privacy (default `aberto`)

**Models:** `SocialPublicacao`, `SocialGrupo`

### 3.9 `armazenamento-nuvem` — Armazenamento em Nuvem

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/armazenamento-nuvem` | `armazenamento-nuvem.dashboard` | `NuvemDashboard` |
| `/dashboard/armazenamento-nuvem/pastas` | `armazenamento-nuvem.pastas` | `NuvemPastas` |
| `/dashboard/armazenamento-nuvem/arquivos` | `armazenamento-nuvem.arquivos` | `NuvemArquivos` |

**Tabelas:**
- `nuvem_folders`: user_id, name, parent_id → `nuvem_folders` (auto-referência)
- `nuvem_files`: user_id, folder_id → `nuvem_folders` (nullable), name, size_kb, is_public (bool), share_token

**Models:** `NuvemPasta`, `NuvemArquivo`

### 3.10 `pdv-vendas` — Vendas / PDV

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/pdv-vendas` | `pdv-vendas.dashboard` | `PdvDashboard` |
| `/dashboard/pdv-vendas/produtos` | `pdv-vendas.produtos` | `PdvProdutos` |
| `/dashboard/pdv-vendas/vendas` | `pdv-vendas.vendas` | `PdvVendas` |

**Tabelas:**
- `pdv_products`: user_id, name, code, price, cost, stock (default 0)
- `pdv_sales`: user_id, customer_name, total, discount (default 0), status (default `concluida`), sold_at

**Models:** `PdvProduto`, `PdvVenda`

### 3.11 `restaurante-mesas` — Restaurante / Controle de Mesas

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/restaurante-mesas` | `restaurante-mesas.dashboard` | `RestauranteDashboard` |
| `/dashboard/restaurante-mesas/mesas` | `restaurante-mesas.mesas` | `RestauranteMesas` |
| `/dashboard/restaurante-mesas/pedidos` | `restaurante-mesas.pedidos` | `RestaurantePedidos` |

**Tabelas:**
- `mesa_tables`: user_id, name, seats (default 4), status (default `livre`)
- `mesa_orders`: user_id, table_id → `mesa_tables` (nullable), items_summary, total (default 0), status (default `aberto`)

**Models:** `RestauranteMesa`, `RestaurantePedido`

### 3.12 `pizzaria-delivery` — Pizzaria Delivery

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/pizzaria-delivery` | `pizzaria-delivery.dashboard` | `PizzariaDashboard` |
| `/dashboard/pizzaria-delivery/produtos` | `pizzaria-delivery.produtos` | `PizzariaProdutos` |
| `/dashboard/pizzaria-delivery/pedidos` | `pizzaria-delivery.pedidos` | `PizzariaPedidos` |

**Tabelas:**
- `pizza_products`: user_id, name, category, price
- `pizza_orders`: user_id, customer_name, delivery_address, total (default 0), status (default `recebido`)

**Models:** `PizzariaProduto`, `PizzariaPedido`

### 3.13 `central-suporte` — Central de Suporte / Tickets

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/central-suporte` | `central-suporte.dashboard` | `SuporteDashboard` |
| `/dashboard/central-suporte/departamentos` | `central-suporte.departamentos` | `SuporteDepartamentos` |
| `/dashboard/central-suporte/tickets` | `central-suporte.tickets` | `SuporteTickets` |

**Tabelas:**
- `suporte_departments`: user_id, name
- `suporte_tickets`: user_id, department_id → `suporte_departments` (nullable), subject, message (text), priority (default `media`), status (default `aberto`)

**Models:** `SuporteDepartamento`, `SuporteTicket`

### 3.14 `site-institucional-cms` — Site Institucional / CMS

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/site-institucional-cms` | `site-institucional-cms.dashboard` | `CmsDashboard` |
| `/dashboard/site-institucional-cms/paginas` | `site-institucional-cms.paginas` | `CmsPaginas` |
| `/dashboard/site-institucional-cms/leads` | `site-institucional-cms.leads` | `CmsLeads` |

**Tabelas:**
- `cms_pages`: user_id, title, slug, content, published (bool, default true)
- `cms_leads`: user_id, name, email, message, status (default `novo`)

**Models:** `CmsPagina`, `CmsLead`

### 3.15 `gestao-arquivos-clientes` — Portal de Arquivos com Clientes

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/gestao-arquivos-clientes` | `gestao-arquivos-clientes.dashboard` | `ArquivosDashboard` |
| `/dashboard/gestao-arquivos-clientes/clientes` | `gestao-arquivos-clientes.clientes` | `ArquivosClientes` |
| `/dashboard/gestao-arquivos-clientes/arquivos` | `gestao-arquivos-clientes.arquivos` | `ArquivosArquivos` |

**Tabelas:**
- `arquivos_clients`: user_id, name, email
- `arquivos_files`: user_id, client_id → `arquivos_clients`, filename, description, downloads_count (default 0)

**Models:** `ArquivosCliente`, `ArquivosArquivo`

### 3.16 `sistema-clinica` — Sistema para Clínicas

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/sistema-clinica` | `sistema-clinica.dashboard` | `ClinicaDashboard` |
| `/dashboard/sistema-clinica/pacientes` | `sistema-clinica.pacientes` | `ClinicaPacientes` |
| `/dashboard/sistema-clinica/consultas` | `sistema-clinica.consultas` | `ClinicaConsultas` |

**Tabelas:**
- `clinica_patients`: user_id, name, phone, birth_date
- `clinica_appointments`: user_id, patient_id → `clinica_patients`, doctor_name, appointment_at (datetime), status (default `agendada`)

**Models:** `ClinicaPaciente`, `ClinicaConsulta`

### 3.17 `controle-empresarial-nfe` — Controle Empresarial (RH + NFe)

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/controle-empresarial-nfe` | `controle-empresarial-nfe.dashboard` | `ErpDashboard` |
| `/dashboard/controle-empresarial-nfe/produtos` | `controle-empresarial-nfe.produtos` | `ErpProdutos` |
| `/dashboard/controle-empresarial-nfe/vendas` | `controle-empresarial-nfe.vendas` | `ErpVendas` |

**Tabelas:**
- `erp_products`: user_id, name, cost, price, stock (default 0)
- `erp_sales`: user_id, client_name, total, nfe_number, sold_at

**Models:** `ErpProduto`, `ErpVenda`

### 3.18 `faturamento-hospedagem` — Faturamento de Hospedagem

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/faturamento-hospedagem` | `faturamento-hospedagem.dashboard` | `HostingDashboard` |
| `/dashboard/faturamento-hospedagem/clientes` | `faturamento-hospedagem.clientes` | `HostingClientes` |
| `/dashboard/faturamento-hospedagem/faturas` | `faturamento-hospedagem.faturas` | `HostingFaturas` |

**Tabelas:**
- `hosting_clients`: user_id, name, email, status (default `ativo`)
- `hosting_invoices`: user_id, client_id → `hosting_clients`, amount, due_date, status (default `pendente`), paid_at

**Models:** `HostingCliente`, `HostingFatura`

### 3.19 `marketplace-leiloes` — Marketplace com Leilões

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/marketplace-leiloes` | `marketplace-leiloes.dashboard` | `MarketplaceDashboard` |
| `/dashboard/marketplace-leiloes/anuncios` | `marketplace-leiloes.anuncios` | `MarketplaceAnuncios` |
| `/dashboard/marketplace-leiloes/lances` | `marketplace-leiloes.lances` | `MarketplaceLances` |

**Tabelas:**
- `marketplace_listings`: user_id, title, description, listing_type (default `fixo`), price, current_bid, status (default `ativo`), ends_at
- `marketplace_bids`: user_id, listing_id → `marketplace_listings`, bidder_name, amount, bid_at

**Models:** `MarketplaceAnuncio`, `MarketplaceLance`

### 3.20 `loja-virtual` — Loja Virtual / E-commerce

**Rotas:**
| Rota | Nome | Componente |
|---|---|---|
| `/dashboard/loja-virtual` | `loja-virtual.dashboard` | `LojaDashboard` |
| `/dashboard/loja-virtual/produtos` | `loja-virtual.produtos` | `LojaProdutos` |
| `/dashboard/loja-virtual/pedidos` | `loja-virtual.pedidos` | `LojaPedidos` |

**Tabelas:**
- `loja_products`: user_id, name, category, price, stock (default 0)
- `loja_orders`: user_id, customer_name, total, status (default `pendente`)

**Models:** `LojaProduto`, `LojaPedido`

---

## 4. Resumo numérico

- **31** pastas originais em `sistemas-php/` → consolidadas em **20** sistemas (removidas duplicatas: emprego/empregos, escola/escola_2018, ordem-servico/mapos-atualizado, email marketing x2, MMN x2, advocacia x2, uber/ubers/taxi, snapchat/socialkit, ucloud/xdrive, e a cópia duplicada de pdv dentro de `sistema_pizza_mesa`).
- **22 migrations** novas (20 módulos + `projects` + seed de `systems`).
- **44 tabelas** de negócio novas (2 a 4 por módulo) + 1 tabela `projects`.
- **44 models** Eloquent novos + `Project`.
- **60 componentes Livewire** novos (20 dashboards + 40 páginas CRUD) + `PublicProjects` + `PublicProjectDetail`.
- **63 rotas** novas sob `/dashboard/...` + **2 rotas públicas** (`/projetos`, `/projetos/{slug}`).
- **20 registros** novos na tabela `systems` (total agora: 23, incluindo os 3 originais `tbr`/`financeiro`/`clientes`).
- **20 registros** na tabela `projects` (vitrine pública).

## 5. Status de verificação

- ✅ `php -l` em todos os arquivos PHP novos — sem erro de sintaxe.
- ✅ `php artisan migrate --force` — todas as 22 migrations aplicadas com sucesso no banco remoto `zrblvt_dev`.
- ✅ `php artisan db:seed --class=ProjectSeeder` — 20 projetos criados.
- ✅ `php artisan route:list` — todas as rotas acima confirmadas registradas.
- ✅ Smoke test via `curl` local: `/projetos` e 5 páginas de detalhe retornaram 200 OK, sem erro no log do servidor.
- ⏳ **Pendente**: teste manual do fluxo autenticado (criar/editar/excluir registro) dentro de cada um dos 20 dashboards — precisa de login real, não foi automatizado (evitar criar conta de teste com privilégio elevado).
