<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function request(Request $request): View
    {
        return view('request.request', [
            'user' => $request->user(),
        ]);
    }
}
