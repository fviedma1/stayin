<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CreateDialog extends Component
{
    public $room;
    public $dateIn;

    public function __construct($room, $dateIn)
    {
        $this->room = $room;
        $this->dateIn = $dateIn;
    }

    public function render()
    {
        return view('components.create-dialog');
    }
}