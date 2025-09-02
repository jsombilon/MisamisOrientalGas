<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function ledger(Request $request): View
    {
        return view('ledger.ledger', [
            'user' => $request->user(),
        ]);
    }
}

