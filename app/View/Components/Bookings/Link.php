<?php

namespace App\View\Components\Bookings;

use Closure;
use App\Models\Event;
use Ixudra\Curl\Facades\Curl;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

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
