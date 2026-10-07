# Changelog — Fusão de sistemas, padronização de UI e revisão de lógica

Documento técnico de tudo que foi feito nesta rodada de trabalho, depois da integração inicial dos 20 módulos (ver `CHANGELOG-INTEGRACAO-SISTEMAS.md`). Cobre: fusão de 3 pares de sistemas, reorganização de arquivos duplicados, dashboard/menu consolidados, seeds automáticos, padronização de selects de localização, editor rich-text, melhoria do CMS e correção de bugs reais encontrados na revisão.

---

## 1. Limpeza de views duplicadas (`tbr`/`financeiro`/`clientes`)

Os componentes Livewire de `tbr`, `financeiro` e `clientes` já apontavam para views dentro de subpastas (`livewire.page.tbr.dashboard`, etc.), mas os arquivos `.blade.php` "soltos" antigos (`tbr-dashboard.blade.php`, `financeiro-categories.blade.php`, `clientes-accounts.blade.php` etc. — 22 arquivos no total) continuavam no repositório sem serem referenciados por nada. Foram apagados após confirmar (via grep) que não havia nenhuma referência restante.

## 2. Home e página de detalhe de projeto

- `/` (home): nova seção "Projetos" mostrando os 4 projetos mais recentes (`Project::latest()->take(4)`) com botão "Ver mais projetos" para `/projetos`.
- `/projetos/{slug}`: corrigido o alinhamento vertical do bloco "Como funciona" (bolinha numerada + texto) — trocado `items-start` por `items-center` no `<li>`.

## 3. Dashboard (`/dashboard`) e menu horizontal

- Removidas as abas antigas TBR/Financeiro/Clientes com gráficos individuais do dashboard central. No lugar, um único bloco **"Todos os Sistemas"** com busca (`wire:model.live`) e um grid com um card por sistema (ícone + nome + contador rápido, ex: "12 vagas", "8 tickets"), clicável direto para o dashboard de cada um.
- `Dashboard.php` foi enxugado: removidas as propriedades/métodos de estatística por aba que não tinham mais consumidor (`loadTbrStats`, `loadFinanceiroStats`, `loadClientesStats` e as ~15 propriedades de gráfico), mantendo só o necessário para os contadores do grid.
- Menu horizontal (`navigation-menu-dashboard.blade.php`): os links fixos "TBR / Financeiro / Clientes" viraram um **dropdown único "Sistemas"** (super admin / usuário sem sistema) listando os 20 sistemas com ícone, mais um link "Gerenciar sistemas" no rodapé do dropdown. Usuário comum continua vendo só o link do seu próprio sistema. Versão mobile replicada como lista agrupada (sem dropdown aninhado).
- Criado `NewModulesNav::allSystems()` como fonte única (slug/label/icon/route) usada tanto pelo grid do dashboard quanto pelo dropdown do menu, pra não haver duas listas divergentes.

## 4. Fusão de 3 pares de sistemas (23 → 20 sistemas)

| Sistema que sumiu | Foi incorporado em | Como |
|---|---|---|
| `faturamento-hospedagem` | `financeiro` | Páginas "Clientes Hospedagem" e "Faturas Hospedagem" viraram itens fixos na sidebar do Financeiro. Dashboard próprio (`HostingDashboard`) removido por redundância. Tabelas `hosting_clients`/`hosting_invoices` mantidas como estão. |
| `gestao-arquivos-clientes` | `armazenamento-nuvem` | Fusão real de tabelas: `arquivos_files` foi migrada para dentro de `nuvem_files` (novas colunas `client_id`, `description`, `downloads_count`) e a tabela antiga foi **dropada**. A tela de arquivos da Nuvem ganhou campo de cliente vinculado, descrição e contador de downloads. "Clientes" virou aba extra da Nuvem (`armazenamento-nuvem.clientes`). |
| `email-marketing` | `marketing-multinivel` | "Listas de Email" e "Campanhas de Email" viraram páginas dentro do MMN (pensado como ferramenta de comunicação com a rede de indicados). Dashboard próprio do email marketing removido. |

Mudanças em cascata para cada fusão:
- **`app/Http/Middleware/CheckSystemAccess.php`**: o prefixo de rota do sistema extinto passou a mapear para o slug do sistema sobrevivente (ex: `dashboard/faturamento-hospedagem` → `financeiro`).
- **`app/Livewire/Page/Dashboard.php`**: removida a entrada de redirect e de contador do sistema extinto.
- **`app/Support/NewModulesNav.php`**: página do sistema extinto passou a viver dentro do array `pages` do sistema sobrevivente; chave de topo do sistema extinto removida.
- **`routes/web.php`**: rotas de dashboard próprio dos sistemas extintos removidas; rotas das páginas que sobreviveram mantidas com os nomes antigos (ex: `faturamento-hospedagem.clientes`) para minimizar risco, só re-parentadas na navegação.
- **Migration `2026_07_04_000002_retire_merged_systems.php`**: reatribui automaticamente qualquer usuário com `system_id` apontando pra um sistema extinto para o sobrevivente, apaga o card correspondente na tabela `projects` (vitrine pública) e remove a linha da tabela `systems`.
- **`database/seeders/ProjectSeeder.php`**: os 3 cards da vitrine correspondentes foram removidos do array-fonte (pra não voltarem em um re-seed); as descrições de `marketing-multinivel` e `armazenamento-nuvem` foram atualizadas pra mencionar as funcionalidades incorporadas.

Resultado: tabela `systems` foi de 23 para **20 registros**; tabela `projects` (vitrine pública) foi de 20 para **17 cards**.

## 5. Contas de admin/usuário para todos os sistemas

`database/seeders/AdminUserSeeder.php` foi generalizado: antes só criava `admin_tbr`, `admin_financeiro`, `admin_cliente` (+ versões `user_`) manualmente. Agora percorre **todos** os registros da tabela `systems` e cria `admin_{slug}@gmail.com` (role admin) e `user_{slug}@gmail.com` (role user) pra cada um, mesma senha/estilo de antes. Rodado uma vez: 41 contas criadas/atualizadas (20 sistemas × 2 + super admin).

## 6. Select de localização em cascata (região → estado → município)

Criada a trait reutilizável `App\Concerns\HasLocationSelect` (baseada no padrão já usado em `TbrEventDetail`, que consulta `App\Services\IbgeService`) e o componente Blade `<x-location-select>`. Aplicado em `EmpregoEmpresas` e `EmpregoVagas` (módulo `vagas-emprego`), que eram os únicos dos 20 módulos com campos **`city` + `state`** de fato separados (os demais módulos com campo de endereço — `ordem-servico`, `gestao-advocacia`, `corridas-mobilidade`, `pizzaria-delivery` — têm um único campo `address` de texto livre, que não é o mesmo tipo de dado e foi mantido como está).

## 7. Editor rich-text nos textareas dos 20 módulos

Criado o componente reutilizável `<x-rich-text-editor>` (extraído do editor `contenteditable` + toolbar que já existia em Clientes — parágrafo/H1-H3/negrito/itálico/sublinhado/tachado/listas/limpar formatação), com id único gerado por instância pra suportar múltiplos editores na mesma página. Substituídos os `<textarea>` simples em 14 telas: `armazenamento-nuvem/arquivos`, `central-suporte/tickets`, `email-marketing/campanhas`, `email-marketing/listas`, `marketing-multinivel/pagamentos`, `marketplace-leiloes/anuncios`, `ordem-servico/ordens`, `rede-social/grupos`, `rede-social/publicacoes`, `restaurante-mesas/pedidos`, `site-institucional-cms/leads`, `site-institucional-cms/paginas`, `vagas-emprego/empresas`, `vagas-emprego/vagas` (2 campos). Onde o mesmo campo aparecia resumido numa tabela (`email-marketing/listas`, `rede-social/grupos`, `rede-social/publicacoes`, `restaurante-mesas/pedidos`), o preview passou a aplicar `strip_tags()` antes do `Str::limit()` pra não mostrar HTML cru na lista.

## 8. Construtor de páginas do CMS (`site-institucional-cms`)

`CmsPaginas.php`: o campo `slug` agora é **gerado automaticamente a partir do título** (`Str::slug`) enquanto o usuário não editar o slug manualmente — a partir do primeiro edit manual, o auto-preenchimento para de sobrescrever. Adicionada validação de unicidade do slug por usuário (`Rule::unique('cms_pages','slug')->where('user_id', ...)->ignore($id)`), que não existia antes. Conteúdo da página passou a usar o editor rich-text (item 7).

## 9. Bugs reais encontrados na revisão de lógica

Revisão sistemática dos 20 módulos (autorização em `edit()`/`executeAction()`, eager loading de relações, paginação) não encontrou problema de autorização (todo mundo já filtra por `user_id` antes do `findOrFail`) nem N+1 de relação. Encontrados e corrigidos **2 bugs reais de colisão de nome de propriedade**, presentes desde a integração original:

- **`NuvemPastas.php`**: a propriedade pública `$folders` (lista completa de pastas, usada no dropdown "pasta pai") tinha o mesmo nome da variável paginada passada por `render()` pra mesma view — a paginação quebrava (`Collection::firstItem() não existe`) porque a propriedade do componente sobrescrevia o dado paginado explícito. Renomeada para `$availableParents`; view atualizada.
- **`MmnMembros.php`**: mesmo problema — `$membros` (lista completa, fonte do dropdown de patrocinador) colidia com a paginação. Renomeada para `$allMembros`.

Ambos confirmados corrigidos via `Livewire::test()` chamando `render()` de ponta a ponta.

## 10. Verificação

- `php -l` limpo em todos os arquivos PHP tocados.
- `php artisan route:list` / `route:cache` sem erro após as fusões (nenhuma rota aponta pra classe inexistente).
- `Livewire::test()` renderizando os 56 componentes dos 20 módulos sem exceção (incluindo os 2 bugs encontrados e corrigidos).
- Teste funcional ponta a ponta do select de localização (região → estado → município) e do editor rich-text via `Livewire::test()`, com dado real persistido e conferido no banco.
- Smoke test via `curl` de `/`, `/projetos`, `/projetos/{slug}` e `/dashboard` (redirect de login) — sem erro no log do servidor.

## Arquivos-chave se precisar mexer depois

- Fusões: `app/Http/Middleware/CheckSystemAccess.php`, `app/Support/NewModulesNav.php`, `app/Livewire/Page/Dashboard.php`, `database/migrations/2026_07_04_000002_retire_merged_systems.php`
- Select de localização: `app/Concerns/HasLocationSelect.php`, `resources/views/components/location-select.blade.php`
- Rich text: `resources/views/components/rich-text-editor.blade.php`
- Seeds: `database/seeders/AdminUserSeeder.php`, `database/seeders/ProjectSeeder.php`
