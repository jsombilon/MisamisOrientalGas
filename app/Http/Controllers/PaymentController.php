<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PaymentController extends Controller
{
     public function payment(Request $request): View
    {
        return view('payment.payment', [
            'user' => $request->user(),
        ]);
    }
}
