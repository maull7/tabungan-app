<?php

namespace App\Actions;

use App\Models\Transaction;
use Illuminate\Http\Response;

class GenerateReceiptPdf
{
    public function handle(Transaction $transaction): Response
    {
        $data = [
            'transaction' => $transaction,
            'account' => $transaction->savingsAccount,
            'user' => $transaction->savingsAccount->user,
        ];

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transactions.receipt', $data)->setPaper('a5');

            return $pdf->stream('receipt-'.$transaction->receipt_number.'.pdf');
        }

        return response()
            ->view('transactions.receipt', $data)
            ->header('Content-Type', 'text/html');
    }
}
