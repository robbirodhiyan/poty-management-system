<?php

namespace App\Http\Controllers\transaction;

use Carbon\Carbon;
use Aws\S3\S3Client;
use App\Models\Debit;
use App\Models\Source;
use App\Models\Product;
use App\Models\Category;
use App\Models\DebitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;


class pemasukan extends Controller
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
    $availableMonths = Debit::distinct()
      ->selectRaw('YEAR(date) as year, MONTH(date) as month')
      ->pluck('year', 'month')
      ->map(function ($year, $month) {
        return \Carbon\Carbon::create($year, $month, 1)->format('Y-m');
      });

    return view('content.transaction.pemasukan', [
      'debits' => $debits,
      'availableMonths' => $availableMonths,
    ]);
  }
  public function create()
  {
    $categories = Category::all();
    $sources = Source::all();
    return view('content.form.form-input-pemasukan', compact('categories', 'sources'));
  }
  public function store(Request $request)
  {
    $request->merge([
      'total' => str_replace('.', '', $request->total)
    ]);

    $request->validate([
      'name' => 'required',
      'description' => 'required',
      'jumlah_item' => 'required|integer',
      'total' => 'required|integer',
      'category' => 'required',
      'source' => 'required',
    ]);

    if (!$request->date) {
      $request->merge([
        'date' => Carbon::now()->format('Y-m-d')
      ]);
    }

    // Simpan data pemasukan
    try {
      DB::beginTransaction();

      // Save Debit data
      $debit = new Debit;
      $debit->name = $request->name;
      $debit->jumlah_item = $request->jumlah_item;
      $debit->description = $request->description;
      $debit->total = $request->total;
      $debit->date = $request->date;
      $debit->save();

      // Save DebitLog data
      $log = new DebitLog;
      $log->debit = $debit->id;
      $log->source = $request->source;
      $log->category = $request->category;
      $log->save();

      // Update stok produk (hanya jika tidak input manual)
      if ($request->input('scanOption') !== 'manualInput') {
        $productName = $request->input('name');
        $quantity = $request->input('jumlah_item');

        $product = Product::where('name_product', $productName)->first();

        if ($product) {
          $product->stok -= $quantity;
          $product->save();
        } else {
          return redirect()->route('pemasukan')->with('error', 'Produk tidak ditemukan');
        }
      }

      DB::commit();

      return redirect()->route('pemasukan')->with('success', 'Pemasukan berhasil disimpan');
    } catch (\Exception $e) {
      DB::rollBack();

      return redirect()->route('pemasukan')->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
    }
  }
  public function edit(string $id)
  {
    // ELOQUENT
    $categories = Category::all();
    $sources = Source::all();
    $debit = Debit::find($id);
    $editUrl = route('editpemasukan', ['debit' => $id]);
    return view('content.form.form-edit-pemasukan', compact(
      'categories',
      'sources',
      'debit',
      'id'
    ));
  }

  public function update(Request $request, string $id)
  {
    // ELOQUENT
    $debit = Debit::find($id);
    // $debit->category_id = $request->category;
    // $debit->source_id = $request->source;
    $debit->save();

    $log = new DebitLog;
    $log->debit = $debit->id;
    $log->source = $request->source;
    $log->category = $request->category;
    $log->save();
    return redirect()->route('pemasukan');
  }

  public function debitLog($id)
  {
    $debitLog = DebitLog::select(
      'debitlog.*',
      'debits.name',
      'debits.total',
      'debits.date',
      // tambahkan kolom status_update,if created_at paling lama status_update=0 else status_update=1
      DB::raw('CASE
      WHEN debitlog.created_at = (
          SELECT MAX(created_at)
          FROM debitlog
          WHERE debitlog.debit = debits.id
      ) AND (
          SELECT COUNT(*)
          FROM debitlog
          WHERE debitlog.debit = debits.id
      ) > 1 THEN 1
      WHEN debitlog.created_at = (
          SELECT MIN(created_at)
          FROM debitlog
          WHERE debitlog.debit = debits.id
      ) THEN 0
      ELSE NULL
  END AS status_update'),
    )
      ->leftJoin('debits', 'debits.id', '=', 'debitlog.debit')
      ->where('debit', $id)
      ->get();

    return response()->json($debitLog);
  }


  public function listS3Files()
  {
    $files = Storage::disk('s3')->allFiles();

    dd($files);
  }
  public function updateStok(Request $request)
  {
    $productName = $request->input('product_name');
    $quantity = $request->input('quantity');

    // Validasi input
    $request->validate([
      'product_name' => 'required|string',
      'quantity' => 'required|integer|min:1',
    ]);

    $product = Product::where('name_product', $productName)->first();

    if ($product) {
      // Kurangkan stok
      $product->stok -= $quantity;

      // Pastikan stok tidak menjadi negatif
      if ($product->stok < 0) {
        return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi'], 422);
      }

      // Simpan perubahan stok
      $product->save();

      return response()->json(['success' => true, 'message' => 'Stok berhasil diperbarui']);
    } else {
      return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
    }
  }
  public function delete(Debit $debit)
  {
    try {
      // Gunakan transaksi untuk memastikan keberlanjutan operasi database
      DB::beginTransaction();

      // Hapus juga log yang terkait
      $debit->debitLog()->delete();

      // Hapus debit
      $debit->delete();

      // Commit transaksi
      DB::commit();

      return response()->json(['success' => 'Debit deleted successfully']);
    } catch (\Exception $e) {
      // Rollback transaksi jika terjadi kesalahan
      DB::rollBack();

      return response()->json(['error' => 'Failed to delete debit'], 500);
    }
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
    $selectedMonth = $request->query('selectedMonth');
    $pdfFileName = 'Laporan Pemasukan';

    if ($selectedMonth && $selectedMonth !== 'all') {
      // Menggunakan where untuk membandingkan tanggal secara langsung
      $debits = Debit::where('date', 'like', $selectedMonth . '%')->get();
      $pdfFileName .= '_' . \Carbon\Carbon::parse($selectedMonth)->format(' F Y');
    } else {
      $debits = Debit::all();
    }
    $selectedMonth = $request->query('selectedMonth');

    $pdf = PDF::loadView('pdf.debit-pdf', compact('debits','selectedMonth'));

    $pdfFileName .= '.pdf';

    return $pdf->download($pdfFileName);
  }
}
