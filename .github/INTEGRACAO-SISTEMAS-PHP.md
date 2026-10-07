# Integração dos 31 sistemas PHP → 20 módulos no IdeiasDev

## O que foi feito

Os 31 diretórios em `sistemas-php/ 31 SISTEMA PHP/` foram lidos, comparados (havia duplicatas e pares do mesmo domínio) e consolidados em **20 sistemas únicos**. Cada um virou um módulo funcional dentro do Laravel `laravel/IdeiasDev`, seguindo exatamente o mesmo padrão já usado pelos módulos existentes `tbr`, `financeiro` e `clientes` (Livewire full-page + migrations + models, com escopo por `system_id`/`user_id`).

Além disso, foi criada uma **vitrine pública** em `/projetos` com cards de todos os 20 sistemas + página de detalhe de cada um.

## 1. Vitrine pública (não precisa login)

- `/projetos` — grid de cards com busca e filtro por categoria
- `/projetos/{slug}` — página de detalhe (descrição, "como funciona", botão "Acessar sistema")
- Link "Projetos" adicionado no menu público (`navigation-menu-web.blade.php`)
- Dados em `database/seeders/ProjectSeeder.php` (tabela `projects`), **já rodado** no banco.

Teste: abra `https://ideias.dev.br/projetos` (ou local) e clique em alguns cards.

## 2. Os 20 módulos funcionais

Cada um tem: Dashboard (estatísticas) + 2 páginas de CRUD (lista + modal criar/editar + excluir com confirmação), sempre em `/dashboard/{slug}` (exige login), e os dados são isolados por usuário (`where('user_id', auth()->id())`).

| # | Slug | Nome | Rotas (`/dashboard/...`) | Tabelas criadas |
|---|------|------|--------------------------|------------------|
| 1 | `vagas-emprego` | Vagas de Emprego | `/vagas-emprego`, `/empresas`, `/vagas` | `emprego_companies`, `emprego_jobs`, `emprego_job_seekers`, `emprego_applications` |
| 2 | `gestao-escolar` | Gestão Escolar/EAD | `/gestao-escolar`, `/turmas`, `/alunos` | `escola_classes`, `escola_students`, `escola_invoices` |
| 3 | `ordem-servico` | Ordem de Serviço | `/ordem-servico`, `/clientes`, `/ordens` | `os_customers`, `os_service_orders`, `os_financial_entries` |
| 4 | `email-marketing` | Email Marketing | `/email-marketing`, `/listas`, `/campanhas` | `email_lists`, `email_subscribers`, `email_campaigns` |
| 5 | `marketing-multinivel` | Marketing Multinível | `/marketing-multinivel`, `/membros`, `/pagamentos` | `mmn_members`, `mmn_payments` |
| 6 | `gestao-advocacia` | Sistema para Advocacia | `/gestao-advocacia`, `/clientes`, `/processos` | `advocacia_clients`, `advocacia_cases` |
| 7 | `corridas-mobilidade` | Corridas/Mobilidade | `/corridas-mobilidade`, `/motoristas`, `/corridas` | `mobi_drivers`, `mobi_rides` |
| 8 | `rede-social` | Rede Social | `/rede-social`, `/publicacoes`, `/grupos` | `social_posts`, `social_groups` |
| 9 | `armazenamento-nuvem` | Armazenamento em Nuvem | `/armazenamento-nuvem`, `/pastas`, `/arquivos` | `nuvem_folders`, `nuvem_files` |
| 10 | `pdv-vendas` | Vendas/PDV | `/pdv-vendas`, `/produtos`, `/vendas` | `pdv_products`, `pdv_sales` |
| 11 | `restaurante-mesas` | Restaurante — Mesas | `/restaurante-mesas`, `/mesas`, `/pedidos` | `mesa_tables`, `mesa_orders` |
| 12 | `pizzaria-delivery` | Pizzaria Delivery | `/pizzaria-delivery`, `/produtos`, `/pedidos` | `pizza_products`, `pizza_orders` |
| 13 | `central-suporte` | Central de Suporte | `/central-suporte`, `/departamentos`, `/tickets` | `suporte_departments`, `suporte_tickets` |
| 14 | `site-institucional-cms` | Site Institucional/CMS | `/site-institucional-cms`, `/paginas`, `/leads` | `cms_pages`, `cms_leads` |
| 15 | `gestao-arquivos-clientes` | Portal de Arquivos com Clientes | `/gestao-arquivos-clientes`, `/clientes`, `/arquivos` | `arquivos_clients`, `arquivos_files` |
| 16 | `sistema-clinica` | Sistema para Clínicas | `/sistema-clinica`, `/pacientes`, `/consultas` | `clinica_patients`, `clinica_appointments` |
| 17 | `controle-empresarial-nfe` | Controle Empresarial (RH+NFe) | `/controle-empresarial-nfe`, `/produtos`, `/vendas` | `erp_products`, `erp_sales` |
| 18 | `faturamento-hospedagem` | Faturamento de Hospedagem | `/faturamento-hospedagem`, `/clientes`, `/faturas` | `hosting_clients`, `hosting_invoices` |
| 19 | `marketplace-leiloes` | Marketplace com Leilões | `/marketplace-leiloes`, `/anuncios`, `/lances` | `marketplace_listings`, `marketplace_bids` |
| 20 | `loja-virtual` | Loja Virtual/E-commerce | `/loja-virtual`, `/produtos`, `/pedidos` | `loja_products`, `loja_orders` |

Observações de nomenclatura corrigidas na pesquisa (bom saber ao ler a copy pública):
- `sistema_projetos em php e mysql` → na verdade é o **ProjectSend** (portal de arquivos com clientes, não gestão de tarefas) → virou `gestao-arquivos-clientes`.
- `Mega_Loja` → é o **CubeCart** rebrandeado → `loja-virtual`.
- `clone mercadolivre` → é o motor de leilões **ProBid** → `marketplace-leiloes`.
- `WHMCS_V5_3_13_Released` → é o **WHMCS 5.3.13** genuíno → `faturamento-hospedagem`.

## 3. Como testar

1. **Cadastro**: em `/register`, o select "Sistema" já lista os 20 novos sistemas automaticamente (a lista vem direto da tabela `systems`, não precisou mexer nessa tela). Crie uma conta escolhendo um dos novos sistemas para testar como usuário comum, ou:
2. **Como super admin** (sua conta atual, `role_id = 1`): acesse direto qualquer `/dashboard/{slug}` da tabela acima sem restrição — o middleware `system.access` libera tudo pra super admin.
3. Em cada dashboard, teste: abrir a página, clicar em "Novo", preencher o formulário, salvar, editar um registro, excluir (com confirmação).
4. O menu lateral (sidebar) mostra os links do módulo automaticamente quando você está dentro dele (ex: entrando em `/dashboard/pdv-vendas`, o menu lateral vira "PDV / Vendas" com Dashboard/Produtos/Vendas).

## 4. O que já foi verificado por mim

- `php -l` em todos os arquivos PHP novos (migrations, models, Livewire) — sem erro de sintaxe.
- `php artisan migrate --force` rodado com sucesso no banco remoto (`zrblvt_dev`) — as 22 migrations novas aplicaram sem erro.
- `php artisan db:seed --class=ProjectSeeder` rodado — 20 registros criados na tabela `projects`, 20 novos registros na tabela `systems` (total 23 sistemas).
- `php artisan route:list` confirma as ~63 rotas novas registradas corretamente.
- Subi um servidor local (`php artisan serve`) e testei via `curl`: `/projetos` (200 OK, cards aparecem) e 5 páginas de detalhe (`/projetos/{slug}`) — todas 200 OK, sem erro no log do servidor.

## 5. O que NÃO foi verificado (fica pra você)

Não testei o fluxo autenticado dentro de cada dashboard (criar/editar/excluir registro via a tela), porque isso exigiria logar como uma conta real ou criar uma conta de teste com privilégio elevado — e criar uma conta admin nova foi bloqueado automaticamente por segurança (evitar backdoor). Então:
- Login manual e clicar pelos 20 módulos é o que falta confirmar.
- Se algo quebrar (erro 500, campo faltando, etc.), me chama de volta com o nome do módulo/página e eu conserto.

## Arquivos-chave se precisar mexer depois

- Rotas: `routes/web.php` (bloco `Route::prefix('dashboard')`)
- Controle de acesso por sistema: `app/Http/Middleware/CheckSystemAccess.php`
- Redirecionamento pós-login: `app/Livewire/Page/Dashboard.php`
- Menu lateral: `resources/views/partials/sidebar-nav.blade.php` + `app/Support/NewModulesNav.php` (array com label/ícone/páginas de cada módulo — editar aqui pra mudar o menu)
- Vitrine pública: `app/Models/Project.php`, `database/seeders/ProjectSeeder.php`, `app/Livewire/Page/PublicProjects.php` / `PublicProjectDetail.php`
- Models e componentes de cada módulo: `app/Models/{Prefixo}*.php` e `app/Livewire/Page/{Prefixo}*.php`
