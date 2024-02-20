<?php

namespace App\Http\Controllers\productions;

use App\Models\Product;
use App\Models\Production;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class ProductionController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $productions = Production::all();
    $availableMonths = Production::distinct()
    ->selectRaw('YEAR(mulai_produksi) as year, MONTH(mulai_produksi) as month')
    ->pluck('year', 'month')
    ->map(function ($year, $month) {
        return \Carbon\Carbon::create($year, $month, 1)->format('Y-m');
    });

return view('content.production.production', [
    'productions' => $productions,
    'availableMonths' => $availableMonths,
]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create($name_product = null)
  {
    $products = Product::all();

    return view('content.form.form-input-production', [
        'products' => $products,
        'name_product' => $name_product,
    ]);

        // Jika tidak ada parameter, contoh: /create-production
        return view('content.form.form-input-production');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $production = new Production();
    $production->kode_produksi = $request->kode_produksi;
    $production->nama_product = $request->nama_product;
    $production->mulai_produksi = $request->mulai_produksi;
    $production->exp_date = $request->exp_date;
    $production->jumlah_produksi = $request->jumlah_produksi;
    // $credit->category_id = $request->category;
    // $credit->source_id = $request->source;

    $production->save();
    $this->updateProductStock($request->nama_product, $request->jumlah_produksi);

    return redirect()->route('production');
}

private function updateProductStock($productName, $productionQuantity)
{
    // Temukan produk berdasarkan nama
    $product = Product::where('name_product', $productName)->first();

    if ($product) {
        // Update stok produk
        $product->stok += $productionQuantity;
        $product->save();
    }
}

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $production = Production::find($id);

    if ($production) {
      $products = Product::all();
      return view('content.form.form-edit-production', compact('production', 'products'));
    } else {
      // Handle jika produksi tidak ditemukan
      return redirect()->route('production.index')->with('error', 'Produksi tidak ditemukan.');
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $request->validate([
      'kode_produksi' => 'required',
      'mulai_produksi' => 'required',
      'exp_date' => 'required',
      'jumlah_produksi' => 'required',
    ]);

    $production = Production::find($id);

    if ($production) {
      $production->kode_produksi = $request->kode_produksi;
      $production->nama_product = $request->nama_product;
      $production->mulai_produksi = $request->mulai_produksi;
      $production->exp_date = $request->exp_date;
      $production->jumlah_produksi = $request->jumlah_produksi;

      $production->save();

      return redirect()->route('production')->with('success', 'Produksi berhasil diperbarui.');
      Alert::success('Sukses', 'Data berhasil diperbarui');
    } else {
      // Handle jika produksi tidak ditemukan
      return redirect()->route('production')->with('error', 'Produksi tidak ditemukan.');
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function delete(Production $production)
  {
    $production->delete();

    return response()->json(['success' => 'Production deleted successfully']);
  }
  public function generatePDF(Request $request)
    {
       $selectedMonth = $request->query('selectedMonth');
       $pdfFileName = 'Laporan Produksi';

    if ($selectedMonth && $selectedMonth !== 'all') {
        // Menggunakan where untuk membandingkan tanggal secara langsung
        $productions = Production::where('mulai_produksi', 'like', $selectedMonth . '%')->get();
        $pdfFileName .= '_' . \Carbon\Carbon::parse($selectedMonth)->format(' F Y');
    } else {
        $productions = Production::all();
    }
    $selectedMonth = $request->query('selectedMonth');

    $pdf = PDF::loadView('pdf.productions-pdf', compact('productions','selectedMonth'));

    $pdfFileName .= '.pdf';

    return $pdf->download($pdfFileName);
    }
}
