<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'slug' => 'vagas-emprego',
                'name' => 'Vagas de Emprego',
                'category' => 'Recrutamento',
                'icon' => 'fa-briefcase',
                'short_description' => 'Portal de empregos que conecta empresas que publicam vagas a candidatos que enviam currículos.',
                'long_description' => 'Sistema de job board no estilo Catho/InfoJobs: empresas se cadastram, escolhem um plano e anunciam vagas, enquanto candidatos criam perfil, cadastram currículo e se candidatam. Resolve a intermediação entre oferta e demanda de emprego em um nicho local ou regional.',
                'workflow' => ['Empresa se cadastra e publica uma vaga', 'Candidato monta o currículo e busca vagas', 'Candidato se candidata à vaga', 'Empresa acessa o painel e avalia as candidaturas'],
            ],
            [
                'slug' => 'gestao-escolar',
                'name' => 'Gestão Escolar / EAD',
                'category' => 'Educação',
                'icon' => 'fa-graduation-cap',
                'short_description' => 'Sistema de gestão acadêmica com alunos, professores, notas e financeiro escolar.',
                'long_description' => 'Atende diretores, professores, alunos e responsáveis: controla turmas, disciplinas, matrícula, boletins, mensalidades (faturas e pagamentos) e avisos. Resolve a centralização das informações acadêmicas e financeiras de uma instituição de ensino.',
                'workflow' => ['Admin cadastra turma, professor e disciplina', 'Professor lança notas de exame', 'Financeiro gera fatura do aluno e registra pagamento', 'Responsável acompanha notas e faturas do filho'],
            ],
            [
                'slug' => 'ordem-servico',
                'name' => 'Ordem de Serviço',
                'category' => 'Assistência Técnica',
                'icon' => 'fa-screwdriver-wrench',
                'short_description' => 'Controle de ordens de serviço, clientes, equipamentos, estoque e financeiro para assistências técnicas.',
                'long_description' => 'Voltado a oficinas e assistências técnicas: abre ordem de serviço vinculada a cliente e equipamento, orça peças e serviços, controla estoque de produtos e lança contas a pagar/receber. Resolve o controle operacional e financeiro de pequenas empresas de manutenção.',
                'workflow' => ['Cliente traz equipamento e técnico abre a OS', 'Técnico orça serviços e peças', 'OS é faturada e gera lançamento financeiro', 'PDV registra vendas avulsas com baixa de estoque'],
            ],
            [
                'slug' => 'marketing-multinivel',
                'name' => 'Marketing Multinível (MMN)',
                'category' => 'Vendas',
                'icon' => 'fa-sitemap',
                'short_description' => 'Plataforma de MMN/ajuda mútua com rede de indicados, controle de repasses e email marketing para a rede.',
                'long_description' => 'Gerencia cadastro de membros, árvore de indicação em cascata, extrato financeiro e comprovantes de pagamento entre participantes. Inclui também listas de contatos e disparo de campanhas de email para comunicar a rede de indicados. Resolve o controle de comissionamento, a comprovação de repasses entre níveis e a comunicação com os membros.',
                'workflow' => ['Visitante se cadastra via link de um indicador', 'Membro paga a adesão e comprova o depósito', 'Indicadores dos níveis acima recebem comissão', 'Admin dispara campanhas de email para engajar a rede'],
            ],
            [
                'slug' => 'gestao-advocacia',
                'name' => 'Sistema para Advocacia',
                'category' => 'Jurídico',
                'icon' => 'fa-scale-balanced',
                'short_description' => 'Gestão de processos jurídicos, clientes, prazos e honorários para escritórios de advocacia.',
                'long_description' => 'CRM jurídico que cadastra processos vinculados a cliente, tribunal e categoria, controla prazos e audiências, honorários e documentos. Resolve a organização de processos e a cobrança de honorários em um escritório de advocacia.',
                'workflow' => ['Escritório cadastra o cliente e abre o processo', 'Advogado registra andamentos e a próxima audiência', 'Sistema agenda compromissos e alerta prazos', 'Honorários são lançados por processo e geram cobrança'],
            ],
            [
                'slug' => 'corridas-mobilidade',
                'name' => 'Corridas / Mobilidade',
                'category' => 'Mobilidade',
                'icon' => 'fa-car-side',
                'short_description' => 'Plataforma de corridas sob demanda que conecta passageiros a motoristas, estilo Uber/Táxi.',
                'long_description' => 'Passageiro solicita corrida informando origem e destino, motoristas disponíveis são localizados e a corrida é aceita e acompanhada em tempo real. A tarifa final é calculada por distância, tempo e categoria do veículo.',
                'workflow' => ['Passageiro define origem/destino e solicita a corrida', 'Motorista disponível aceita a solicitação', 'Corrida é rastreada até o embarque e finalização', 'Tarifa final é calculada e passageiro/motorista se avaliam'],
            ],
            [
                'slug' => 'rede-social',
                'name' => 'Rede Social',
                'category' => 'Comunidade',
                'icon' => 'fa-users',
                'short_description' => 'Rede social com perfis, publicações, amigos e mensagens efêmeras (stories).',
                'long_description' => 'Cobre cadastro e perfil, amizade/seguidores, publicações com mídia na timeline, grupos e páginas, além de stories que expiram após visualização. Voltado a quem quer lançar uma comunidade própria sem depender de plataformas de terceiros.',
                'workflow' => ['Usuário cria o perfil (foto, capa, bio)', 'Envia/aceita solicitações de amizade ou segue perfis', 'Publica posts e interage com curtidas e comentários', 'Envia stories efêmeros que expiram após um tempo'],
            ],
            [
                'slug' => 'armazenamento-nuvem',
                'name' => 'Armazenamento em Nuvem',
                'category' => 'Produtividade',
                'icon' => 'fa-cloud-arrow-up',
                'short_description' => 'Upload e compartilhamento de arquivos com pastas, clientes vinculados, links públicos e planos por espaço.',
                'long_description' => 'Serviço tipo Dropbox/MediaFire: usuários organizam arquivos em pastas, vinculam arquivos a um cliente para compartilhamento controlado (com contagem de downloads), geram links de compartilhamento com senha opcional e podem assinar planos com mais espaço de armazenamento.',
                'workflow' => ['Usuário recebe uma cota de armazenamento gratuita', 'Faz upload de arquivos e organiza em pastas', 'Vincula um arquivo a um cliente ou gera link público', 'Assina um plano premium para aumentar o limite'],
            ],
            [
                'slug' => 'pdv-vendas',
                'name' => 'Vendas / PDV',
                'category' => 'Varejo',
                'icon' => 'fa-cash-register',
                'short_description' => 'Ponto de venda para pequenos comércios, com caixa, estoque e vendas.',
                'long_description' => 'Cadastro de produtos com controle de estoque e alerta de quantidade mínima, abertura/fechamento de caixa e vendas com desconto. Voltado a comerciantes que precisam de uma frente de caixa simples.',
                'workflow' => ['Operador abre o caixa informando o valor inicial', 'Registra a venda e aplica desconto se necessário', 'Sistema baixa o estoque automaticamente', 'Operador fecha o caixa conferindo os valores'],
            ],
            [
                'slug' => 'restaurante-mesas',
                'name' => 'Restaurante — Controle de Mesas',
                'category' => 'Alimentação',
                'icon' => 'fa-utensils',
                'short_description' => 'Controle de mesas, comandas e guichê de retirada para restaurantes e lanchonetes.',
                'long_description' => 'Cadastro de mesas com status de ocupação, lançamento de pedidos com itens do cardápio e painel de guichê que exibe o pedido pronto para retirada. Atende restaurantes que querem gerenciar o salão sem depender de papel.',
                'workflow' => ['Garçom abre uma mesa e lança o pedido', 'Pedido é enviado à cozinha/guichê', 'Cliente é chamado quando o pedido fica pronto', 'Fechamento calcula o total e libera a mesa'],
            ],
            [
                'slug' => 'pizzaria-delivery',
                'name' => 'Pizzaria Delivery',
                'category' => 'Alimentação',
                'icon' => 'fa-pizza-slice',
                'short_description' => 'Site de pedidos online de pizzaria com montagem de pizza meio a meio.',
                'long_description' => 'Catálogo de produtos por categoria, montagem de pizza combinando dois sabores, adicionais pagos e cadastro de clientes com endereço de entrega. Ideal para pequenos deliveries que precisam de um cardápio digital.',
                'workflow' => ['Cliente navega o cardápio por categoria', 'Monta a pizza inteira ou meio a meio com adicionais', 'Informa endereço e finaliza o pedido', 'Admin gerencia o cardápio e recebe os pedidos'],
            ],
            [
                'slug' => 'central-suporte',
                'name' => 'Central de Suporte / Tickets',
                'category' => 'Atendimento',
                'icon' => 'fa-headset',
                'short_description' => 'Abertura e acompanhamento de tickets de suporte técnico por departamento.',
                'long_description' => 'Clientes abrem tickets categorizados por departamento e prioridade, e a equipe responde através de um histórico de interações até a resolução. Atende empresas que precisam organizar o suporte ao cliente.',
                'workflow' => ['Cliente abre um ticket escolhendo departamento e prioridade', 'Equipe assume o ticket e responde', 'Histórico de respostas fica registrado até a resolução', 'Cliente acompanha o status e pode reabrir o ticket'],
            ],
            [
                'slug' => 'site-institucional-cms',
                'name' => 'Site Institucional / CMS',
                'category' => 'Marketing',
                'icon' => 'fa-building-columns',
                'short_description' => 'CMS para site institucional com portfólio, serviços, depoimentos e blog.',
                'long_description' => 'Páginas de conteúdo organizadas por área, portfólio de projetos, serviços oferecidos, depoimentos de clientes e formulário de contato com registro de leads. Um CMS institucional genérico reutilizável para qualquer empresa de serviços.',
                'workflow' => ['Visitante navega páginas institucionais e o portfólio', 'Visitante envia um lead pelo formulário de contato', 'Admin gerencia páginas, serviços e depoimentos', 'Admin acompanha os leads recebidos'],
            ],
            [
                'slug' => 'sistema-clinica',
                'name' => 'Sistema para Clínicas',
                'category' => 'Saúde',
                'icon' => 'fa-stethoscope',
                'short_description' => 'Gestão de consultas, pacientes, médicos e prontuários para clínicas.',
                'long_description' => 'Centraliza cadastro de pacientes, agenda de consultas, prescrições médicas e faturamento de atendimentos. Usado por recepcionistas, médicos e administração para reduzir a papelada.',
                'workflow' => ['Recepção cadastra o paciente', 'Consulta é agendada com um médico', 'Médico registra a prescrição e o histórico', 'Sistema gera a fatura do atendimento'],
            ],
            [
                'slug' => 'controle-empresarial-nfe',
                'name' => 'Controle Empresarial (RH + NFe)',
                'category' => 'Gestão Empresarial',
                'icon' => 'fa-building',
                'short_description' => 'ERP para PMEs com financeiro, estoque, RH e emissão de Nota Fiscal Eletrônica.',
                'long_description' => 'Unifica cadastro de clientes e fornecedores, controle de estoque e vendas, folha de pagamento e emissão de NFe conforme a legislação fiscal brasileira. Substitui planilhas soltas e integra a operação com obrigações fiscais.',
                'workflow' => ['Cadastro de funcionários, clientes e fornecedores', 'Compra de mercadorias entra no estoque', 'Venda ao cliente baixa o estoque', 'NFe é emitida vinculada à venda'],
            ],
            [
                'slug' => 'marketplace-leiloes',
                'name' => 'Marketplace com Leilões',
                'category' => 'E-commerce',
                'icon' => 'fa-gavel',
                'short_description' => 'Marketplace multi-vendedor onde qualquer usuário anuncia produtos por preço fixo ou leilão.',
                'long_description' => 'Qualquer usuário pode se tornar vendedor e anunciar produtos em categorias, vendendo por preço fixo ou leilão com lances. Resolve a necessidade de um shopping online onde terceiros vendem sem a plataforma manter estoque próprio.',
                'workflow' => ['Usuário se cadastra e se torna vendedor', 'Vendedor cria um anúncio de preço fixo ou leilão', 'Compradores dão lances ou compram direto', 'Plataforma fecha o leilão e cobra a comissão do vendedor'],
            ],
            [
                'slug' => 'loja-virtual',
                'name' => 'Loja Virtual / E-commerce',
                'category' => 'E-commerce',
                'icon' => 'fa-store',
                'short_description' => 'Loja virtual completa com catálogo, carrinho, pedidos e clientes.',
                'long_description' => 'Gestão de catálogo por categorias e marcas, carrinho de compras e acompanhamento de pedidos do início ao envio. Voltado a pequenas e médias lojas que precisam de uma vitrine própria.',
                'workflow' => ['Cliente navega o catálogo por categoria', 'Adiciona produtos ao carrinho e finaliza o pedido', 'Loja processa o pagamento e atualiza o status', 'Loja despacha o pedido e atualiza o estoque'],
            ],
        ];

        foreach ($projects as $i => $project) {
            Project::updateOrCreate(
                ['slug' => $project['slug']],
                $project + ['system_slug' => $project['slug'], 'sort_order' => $i]
            );
        }
    }
}
