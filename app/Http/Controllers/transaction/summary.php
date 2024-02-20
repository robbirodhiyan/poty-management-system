<?php

namespace App\Http\Controllers\transaction;

use App\Models\Debit;
use App\Models\Credit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class summary extends Controller
{
  public function index()
  {
    $debits = Debit::select(['debits.*', 'debitlog.source', 'debitlog.category'])
      ->selectSub(function ($query) {
        $query->selectRaw('COUNT(*) > 1')
          ->from('debitlog')
          ->whereRaw('debitlog.debit = debits.id');
      }, 'status_update')
      ->join('debitlog', function ($join) {
        $join->on('debitlog.debit', '=', 'debits.id')
          ->whereRaw('debitlog.id = (select max(id) from debitlog where debitlog.debit = debits.id)');
      })
      ->get();
    $credits = Credit::select(['credits.*', 'creditlog.source', 'creditlog.category'])
      ->selectSub(function ($query) {
        $query->selectRaw('COUNT(*) > 1')
          ->from('creditlog')
          ->whereRaw('creditlog.credit = credits.id');
      }, 'status_update')
      ->join('creditlog', function ($join) {
        $join->on('creditlog.credit', '=', 'credits.id')
          ->whereRaw('creditlog.id = (select max(id) from creditlog where creditlog.credit = credits.id)');
      })
      ->get();
    $debitMonths = Debit::distinct()
      ->selectRaw('YEAR(date) as year, MONTH(date) as month')
      ->pluck('year', 'month')
      ->map(function ($year, $month) {
        return \Carbon\Carbon::create($year, $month, 1)->format('Y-m');
      });

    $creditMonths = Credit::distinct()
      ->selectRaw('YEAR(date) as year, MONTH(date) as month')
      ->pluck('year', 'month')
      ->map(function ($year, $month) {
        return \Carbon\Carbon::create($year, $month, 1)->format('Y-m');
      });

    $availableMonths = $debitMonths->merge($creditMonths)->unique()->sort();
    $transactions = $debits->concat($credits);


    return view('content.transaction.summary', [
      'debits' => $debits,
      'credits' => $credits,
      'transactions' => $transactions,
      'availableMonths' => $availableMonths
    ]);
  }
  public function generatePDF(Request $request)
{
    $debits = Debit::select(['debits.*', 'debitlog.source', 'debitlog.category'])
        ->selectSub(function ($query) {
            $query->selectRaw('COUNT(*) > 1')
                ->from('debitlog')
                ->whereRaw('debitlog.debit = debits.id');
        }, 'status_update')
        ->join('debitlog', function ($join) {
            $join->on('debitlog.debit', '=', 'debits.id')
                ->whereRaw('debitlog.id = (select max(id) from debitlog where debitlog.debit = debits.id)');
        })
        ->get();

    $credits = Credit::select(['credits.*', 'creditlog.source', 'creditlog.category'])
        ->selectSub(function ($query) {
            $query->selectRaw('COUNT(*) > 1')
                ->from('creditlog')
                ->whereRaw('creditlog.credit = credits.id');
        }, 'status_update')
        ->join('creditlog', function ($join) {
            $join->on('creditlog.credit', '=', 'credits.id')
                ->whereRaw('creditlog.id = (select max(id) from creditlog where creditlog.credit = credits.id)');
        })
        ->get();

    $selectedMonth = $request->query('selectedMonth');
    $pdfFileName = 'Laporan Keuangan';
    $mergedData = $debits->merge($credits);

    if ($selectedMonth && $selectedMonth !== 'all') {
        $mergedData = $mergedData->filter(function ($transaction) use ($selectedMonth) {
            return \Carbon\Carbon::parse($transaction->date)->format('Y-m') == $selectedMonth;
        });
        $pdfFileName .= '_' . \Carbon\Carbon::parse($selectedMonth)->format(' F Y');
    }

    $pdf = PDF::loadView('pdf.summary-pdf', compact('mergedData', 'debits', 'credits', 'selectedMonth'));

    $pdfFileName .= '.pdf';

    return $pdf->download($pdfFileName);
}
}
