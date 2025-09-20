<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class OrderController extends Controller
{
    public function print(Order $order)
    {
        $order->load(['client', 'items.product']);
        $pdf = DomPDF::loadView('pdf.order', compact('order'));
        return $pdf->download('order_'.$order->order_slip.'.pdf');
    }
}
