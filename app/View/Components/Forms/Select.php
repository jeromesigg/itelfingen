<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public string $name;
    public string $label;
    public string $id;
    public bool $required;
    public $collection;
    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $label = '', string $id = '', bool $required = false, $collection = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->collection = $collection;
        $this->id = $id ?? $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.select');
    }
}
