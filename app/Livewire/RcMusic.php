<?php

namespace App\Livewire;

use Livewire\Component;

class RcMusic extends Component
{
    public bool $navigate = false;

    public function render()
    {
        return view('livewire.rc-music')->layout('layouts.app-navigate', ['navigate' => $this->navigate]);;
    }
}
