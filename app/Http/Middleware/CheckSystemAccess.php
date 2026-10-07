<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            throw new AuthenticationException('Unauthenticated.');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $path = $request->path();
        $userSystem = $user->system?->slug;

        $systemRoutes = [
            'dashboard/tbr'                       => 'tbr',
            'dashboard/financeiro'                => 'financeiro',
            'dashboard/clientes'                  => 'clientes',
            'dashboard/vagas-emprego'              => 'vagas-emprego',
            'dashboard/gestao-escolar'              => 'gestao-escolar',
            'dashboard/ordem-servico'               => 'ordem-servico',
            // email-marketing foi incorporado ao marketing-multinivel (um só sistema)
            'dashboard/email-marketing'              => 'marketing-multinivel',
            'dashboard/marketing-multinivel'          => 'marketing-multinivel',
            'dashboard/gestao-advocacia'             => 'gestao-advocacia',
            'dashboard/corridas-mobilidade'           => 'corridas-mobilidade',
            'dashboard/rede-social'                  => 'rede-social',
            'dashboard/armazenamento-nuvem'           => 'armazenamento-nuvem',
            'dashboard/pdv-vendas'                    => 'pdv-vendas',
            'dashboard/restaurante-mesas'             => 'restaurante-mesas',
            'dashboard/pizzaria-delivery'             => 'pizzaria-delivery',
            'dashboard/central-suporte'               => 'central-suporte',
            'dashboard/site-institucional-cms'        => 'site-institucional-cms',
            // gestao-arquivos-clientes foi incorporado ao armazenamento-nuvem (um só sistema)
            'dashboard/gestao-arquivos-clientes'      => 'armazenamento-nuvem',
            'dashboard/sistema-clinica'               => 'sistema-clinica',
            'dashboard/controle-empresarial-nfe'      => 'controle-empresarial-nfe',
            // faturamento-hospedagem foi incorporado ao financeiro (um só sistema)
            'dashboard/faturamento-hospedagem'        => 'financeiro',
            'dashboard/marketplace-leiloes'           => 'marketplace-leiloes',
            'dashboard/loja-virtual'                  => 'loja-virtual',
            'dashboard/admin'                        => 'admin',
        ];

        $matchedSystem = null;
        foreach ($systemRoutes as $prefix => $slug) {
            if (str_starts_with($path, $prefix)) {
                $matchedSystem = $slug;
                break;
            }
        }

        if (!$matchedSystem) {
            return $next($request);
        }

        if ($matchedSystem === 'admin') {
            abort(403, 'Acesso restrito ao Super Admin.');
        }

        if ($matchedSystem !== $userSystem) {
            abort(403, 'Você não tem acesso a este sistema.');
        }

        return $next($request);
    }
}
