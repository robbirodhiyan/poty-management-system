<?php

namespace App\Http\Controllers\transaction;

use Carbon\Carbon;
use App\Models\Credit;
use App\Models\Source;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CreditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;


class pengeluaran extends Controller
{
  public function index()
  {
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
    $availableMonths = Credit::distinct()
      ->selectRaw('YEAR(date) as year, MONTH(date) as month')
      ->pluck('year', 'month')
      ->map(function ($year, $month) {
        return \Carbon\Carbon::create($year, $month, 1)->format('Y-m');
      });

    return view('content.transaction.pengeluaran', [
      'credits' => $credits,
      'availableMonths' => $availableMonths,
    ]);
  }
  public function create()
  {
    // ELOQUENT
    $categories = Category::all();
    $sources = Source::all();
    return view('content.form.form-input-pengeluaran', compact('categories', 'sources'));
  }
  public function store(Request $request)
  {
    $request->merge([
      'total' => str_replace('.', '', $request->total)
    ]);
    $request->validate([
      'name' => 'required',
      'description' => 'required',
      'total' => 'required|integer',
      'category' => 'required',
      'source' => 'required',
    ]);
    if (!$request->date) {
      $request->merge([
        'date' => Carbon::now()->format('Y-m-d')
      ]);
      $request->validate([
        'date' => 'required|date'
      ]);
    }

    // ELOQUENT
    $credit = new Credit;
    $credit->name = $request->name;
    $credit->description = $request->description;
    $credit->total = $request->total;

    // $credit->category_id = $request->category;
    // $credit->source_id = $request->source;
    $credit->date = $request->date;
    $credit->save();

    $log = new CreditLog;
    $log->credit = $credit->id;
    $log->category = $request->category;
    $log->source = $request->source;
    $log->save();
    return redirect()->route('pengeluaran');
  }
  public function edit(string $id)
  {
    // ELOQUENT
    $categories = Category::all();
    $sources = Source::all();
    $credit = Credit::find($id);
    $editUrl = route('editpengeluaran', ['credit' => $id]);
    return view('content.form.form-edit-pengeluaran', compact(
      'categories',
      'sources',
      'credit',
      'id'
    ));
  }

  public function update(Request $request, string $id)
  {
    // ELOQUENT
    $credit = Credit::find($id);
    // $credit->category_id = $request->category;
    // $credit->source_id = $request->source;
    $credit->save();

    $log = new CreditLog;
    $log->credit = $credit->id;
    $log->category = $request->category;
    $log->source = $request->source;
    $log->save();
    return redirect()->route('pengeluaran');
  }

  public function creditLog($id)
  {
    $creditLog = CreditLog::select(
      'creditlog.*',
      'credits.name',
      'credits.total',
      'credits.date',
      // tambahkan kolom status_update,if created_at paling lama status_update=0 else status_update=1
      DB::raw('CASE
      WHEN creditlog.created_at = (
          SELECT MAX(created_at)
          FROM creditlog
          WHERE creditlog.credit = credits.id
      ) AND (
          SELECT COUNT(*)
          FROM creditlog
          WHERE creditlog.credit = credits.id
      ) > 1 THEN 1
      WHEN creditlog.created_at = (
          SELECT MIN(created_at)
          FROM creditlog
          WHERE creditlog.credit = credits.id
      ) THEN 0
      ELSE NULL
  END AS status_update'),
    )
      ->leftJoin('credits', 'credits.id', '=', 'creditlog.credit')
      ->where('credit', $id)
      ->get();

    return response()->json($creditLog);
  }
  public function delete(Credit $credit)
  {
    try {
      // Gunakan transaksi untuk memastikan keberlanjutan operasi database
      DB::beginTransaction();

      // Hapus juga log yang terkait
      $credit->creditLog()->delete();

      // Hapus debit
      $credit->delete();

      // Commit transaksi
      DB::commit();

      return response()->json(['success' => 'Credit deleted successfully']);
    } catch (\Exception $e) {
      // Rollback transaksi jika terjadi kesalahan
      DB::rollBack();

      return response()->json(['error' => 'Failed to delete credit'], 500);
    }
  }
  public function generatePDF(Request $request)
  {
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
    $pdfFileName = 'Laporan Pengeluaran';

    if ($selectedMonth && $selectedMonth !== 'all') {
      // Menggunakan where untuk membandingkan tanggal secara langsung
      $debits = Credit::where('date', 'like', $selectedMonth . '%')->get();
      $pdfFileName .= '_' . \Carbon\Carbon::parse($selectedMonth)->format(' F Y');
    } else {
      $debits = Credit::all();
    }
    $selectedMonth = $request->query('selectedMonth');

    $pdf = PDF::loadView('pdf.credit-pdf', compact('credits','selectedMonth'));

    $pdfFileName .= '.pdf';

    return $pdf->download($pdfFileName);
  }
}
