<?php

namespace App\View\Components;

use Illuminate\View\Component;

class HotelComponent extends Component
{
    public $hotel;

    /**
     * Create a new component instance.
     *
     * @param  \App\Models\Hotel  $hotel
     * @return void
     */
    public function __construct($hotel)
    {
        $this->hotel = $hotel;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.hotel-component');
    }
}
