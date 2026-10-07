<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class OpenGraphTags extends Component
{
    // Parametros
    public $title;
    public $description;
    public $image;
    public $url;

    // Receber os parâmetros do controlador ou página
    public function __construct($title = null, $description = null, $image = null, $url = null)
    {
        $this->title = $title ?? config('app.name');
        $this->description = $description ?? 'Aqui vai a descrição padrão';
        $this->image = $image ?? asset('images/default-image.jpg'); 
        $this->url = $url ?? url()->current(); 
    }

    public function render(): View|Closure|string
    {
        return view('components.open-graph-tags');
    }
}
