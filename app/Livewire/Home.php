<?php

namespace App\Livewire;

use Livewire\Component;
use Laravel\Jetstream\InteractsWithBanner;

class Home extends Component
{
    use InteractsWithBanner;

    public bool $navigate = false;

    public $name = '';
    public $email = '';
    public $phone = '';
    public $domain = '';
    public $url = '';
    public $desc = '';

    // Regras de validação
    protected function rules()
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'min:9'],
            'domain' => ['required'],
            'url' => ['url'],
            'desc' => ['required', 'min:10'],
        ];
    }

    // Mensagens de erro personalizadas
    protected function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'phone.required' => 'O campo telefone é obrigatório.',
            'phone.min' => 'O telefone deve ter no mínimo 9 caracteres.',
            'domain.required' => 'O campo domínio é obrigatório.',
            'url.url' => 'Informe uma URL válida.',
            'desc.required' => 'O campo descrição é obrigatório.',
            'desc.min' => 'A descrição deve ter no mínimo 10 caracteres.',
        ];
    }

    public function submit_form()
    {
        $this->validate();

        $this->banner('Formulário enviado com sucesso!');

        $this->reset();
    }

    public function render()
    {
        return view('livewire.home')
            ->layout('layouts.app-navigate', [
                'navigate' => $this->navigate,
            ]);
    }
}
