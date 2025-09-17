<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


class RoomDialog extends Component
{
    public $room;

    public function __construct($room)
    {
        $this->room = $room;
    }

    public function render()
    {
        return view('components.room-dialog');
    }
}
