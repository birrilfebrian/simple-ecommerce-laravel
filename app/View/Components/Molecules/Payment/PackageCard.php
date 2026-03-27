<?php

namespace App\View\Components\Molecules\Payment;

use Illuminate\View\Component;

class PackageCard extends Component
{
    // Daftarkan variabel agar bisa diakses di Blade
    public $credits;
    public $price;

    /**
     * Create a new component instance.
     */
    public function __construct($credits = 0, $price = 0)
    {
        $this->credits = $credits;
        $this->price = $price;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('client.components.molecules.payment.package-card');
    }
}
