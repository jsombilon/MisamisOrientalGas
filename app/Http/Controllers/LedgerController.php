<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Ledger;
use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use Carbon\Carbon;

class LedgerController extends Controller
{

   public function asd()
    {
      return view('asd');
    }
}