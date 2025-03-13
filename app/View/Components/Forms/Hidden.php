<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Hidden extends Component
{
    public string $name;
    public string $id;
    public string $label;
    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $label = '', string $id = null)
    {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.hidden');
    }
}
