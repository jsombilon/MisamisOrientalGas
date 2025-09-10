<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Ledger;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class LedgerController extends Controller
{
    public function exportDocx(Request $request, Client $client)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // Get all ledger entries for the client for the month/year
        $ledgers = Ledger::where('client_id', $client->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at')
            ->get();

        // Calculate balance forwarded (previous outstanding)
        $prevBalance = Ledger::where('client_id', $client->id)
            ->where(function($q) use ($year, $month) {
                $q->whereYear('created_at', '<', $year)
                  ->orWhere(function($q2) use ($year, $month) {
                      $q2->whereYear('created_at', $year)
                         ->whereMonth('created_at', '<', $month);
                  });
            })
            ->orderBy('created_at', 'desc')
            ->first()?->balance ?? 0;

        // Calculate total balance (last entry of the month)
        $totalBalance = $ledgers->last()?->balance ?? $prevBalance;

        // Create DOCX
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Client Info
        $section->addText("Client Ledger", ['bold' => true, 'size' => 16]);
        $section->addText("Client Number: {$client->client_number}");
        $section->addText("Name: {$client->client_name}");
        $section->addText("Address: {$client->location}");
        $section->addText("Payment Type: {$client->payment_type}");
        $section->addText("Period: " . date('F Y', mktime(0,0,0,$month,1,$year)));
        $section->addTextBreak();

        // Table
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $table->addRow();
        $table->addCell()->addText('Date');
        $table->addCell()->addText('Order Slip');
        $table->addCell()->addText('Debit');
        $table->addCell()->addText('Credit');
        $table->addCell()->addText('Balance');
        $table->addCell()->addText('Remarks');
        $table->addCell()->addText('Payment');
        $table->addCell()->addText('Ordered Products');

        // Balance Forwarded
        $table->addRow();
        $table->addCell()->addText('');
        $table->addCell()->addText('Balance Forwarded');
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText(number_format($prevBalance, 2));
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');

        // Transactions
        foreach ($ledgers as $ledger) {
            $products = '';
            if ($ledger->order_id && $ledger->order) {
                $products = $ledger->order->items->map(function($item) {
                    return $item->product->product_name . ' x' . $item->quantity;
                })->implode(', ');
            }
            $table->addRow();
            $table->addCell()->addText($ledger->created_at->format('Y-m-d'));
            $table->addCell()->addText($ledger->order?->order_slip ?? '');
            $table->addCell()->addText($ledger->debit ? number_format($ledger->debit, 2) : '');
            $table->addCell()->addText($ledger->credit ? number_format($ledger->credit, 2) : '');
            $table->addCell()->addText(number_format($ledger->balance, 2));
            $table->addCell()->addText($ledger->remarks ?? '');
            $table->addCell()->addText($ledger->payment?->payment_type ?? '');
            $table->addCell()->addText($products);
        }

        // Total Balance
        $section->addTextBreak();
        $section->addText("Total Balance: " . number_format($totalBalance, 2), ['bold' => true]);

        // Output DOCX
        $filename = "Ledger-{$client->client_number}-{$year}-{$month}.docx";
        $tempFile = tempnam(sys_get_temp_dir(), 'ledger');
        $phpWordWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $phpWordWriter->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}