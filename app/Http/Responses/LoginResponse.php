<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return redirect('/dashboard');
        }

        $redirects = [
            'tbr' => '/dashboard/tbr',
            'financeiro' => '/dashboard/financeiro',
            'clientes' => '/dashboard/clientes',
        ];

        $slug = $user->system?->slug;
        $redirect = $redirects[$slug] ?? '/dashboard';

        return redirect($redirect);
    }
}
