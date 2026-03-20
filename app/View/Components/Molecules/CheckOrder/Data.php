<?php

namespace App\View\Components\Molecules\CheckOrder;

use Illuminate\View\Component;

class Data extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $order, $orderDetail, $pesanWA, $nomorAdmin;

    public function __construct($order, $orderDetail, $pesanWA, $nomorAdmin)
    {
        $this->order = $order;
        $this->orderDetail = $orderDetail;
        $this->nomorAdmin = $nomorAdmin;
        $this->pesanWA = $pesanWA;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('client.components.molecules.check-order.data');
    }
}
