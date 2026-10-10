<?php

namespace App\View\Components\Bookings;

use App\Models\Event;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{
    public string $type;

    public $contents;

    public Event $event;

    /**
     * Create a new component instance.
     */
    public function __construct(string $type, Event $event, $contents)
    {
        //
        $this->event = $event;
        $this->type = $type;
        $this->contents = $contents;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.bookings.link');
    }
}
