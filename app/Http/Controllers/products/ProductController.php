<?php

namespace App\Http\Controllers\Products;

use DateTime;
use Mpdf\Mpdf;
use App\Models\Product;
use App\Models\Production;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\GenerateProductPdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;



class ProductController extends Controller
{
  public function getProductData(Request $request)
  {
    $nameProduct = $request->input('name_product');

    // Cari produk berdasarkan name_product
    $product = Product::where('name_product', $nameProduct)->first();

    if ($product) {
      // Jika produk ditemukan, kirim data sebagai respons JSON
      return response()->json([
        'harga_jual' => $product->harga_jual,
        'stok' => $product->stok,
        'name_product' => $product->name_product,
      ]);
    } else {
      // Jika produk tidak ditemukan, kirim respons kosong
      return response()->json([]);
    }
  }
  public function index()
  {
    $products = Product::all();
    // Menambahkan kolom kode_produksi, exp_date, dan hpp dari tabel productions
    foreach ($products as $product) {
      $productionData = Production::find($product->production_id);
      $product->kode_produksi = $productionData->kode_produksi;
      $product->exp_date = $productionData->exp_date;
      $product->hpp = $productionData->hpp;
    }
    foreach ($products as $product) {
      $product->days_until_expiry = $this->calculateDaysUntilExpiry($product->exp_date);
    }
    return view('content.product.product', ['products' => $products]);
  }


  public function create()
  {
    $productions = Production::all();
    return view('content.form.form-input-product', compact('productions'));
  }


  public function store(Request $request)
  {
    $request->validate([
      'name_product' => 'required|exists:productions,nama_product',
      'harga_jual' => 'required|numeric',
      'stok' => 'required|numeric',
      'gambar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Dapatkan data produksi berdasarkan nama_product
    $production = Production::where('nama_product', $request->name_product)->first();


    // Simpan data produk
    $product = new Product;
    $product->gambar = $request->gambar;
    $product->name_product = $request->name_product;
    $product->harga_jual = $request->harga_jual;
    $product->stok = $request->stok;

    // Set FK production_id berdasarkan ID dari data produksi
    $product->production_id = $production->id;

    $product->save();

    if ($request->hasFile('gambar')) {
      $imagePath = $request->file('gambar')->store('images', 'public');
      $product->gambar = $imagePath;
      $product->save();
    }

    return redirect()->route('product');
  }

  public function show($id)
  {
    //
  }


  public function edit($id)
  {
    $product = Product::find($id);
    $productions = Production::all();
    return view('content.form.form-edit-product', compact('product', 'productions'));
  }


  public function update(Request $request, $id)
  {
    $product = Product::find($id);

    // Validasi formulir
    $request->validate([
      'name_product' => 'required',
      'stok' => 'required',
      'harga_jual' => 'required',
    ]);

    // Jika ada gambar baru diunggah
    if ($request->hasFile('gambar')) {
      // Validasi gambar
      $request->validate([
        'gambar' => 'image|mimes:jpeg,jpg,png|max:2048',
      ]);

      // Simpan gambar baru
      $gambarPath = $request->file('gambar')->store('public/gambar_produk');
      $product->gambar = basename($gambarPath);
    }

    // Update data lainnya
    $product->name_product = $request->name_product;
    $product->stok = $request->stok;
    $product->harga_jual = $request->harga_jual;

    // Simpan perubahan
    $product->save();

    return redirect()->route('product')->with('success', 'Product updated successfully');
  }

public function delete(Product $product)
{
    $product->delete();

    return response()->json(['success' => 'Product deleted successfully']);
}
  public function destroy($id)
  {
    $product = Product::find($id);
    $product->delete();

    return redirect()->route('product');
  }

  public function getProduksi(Request $request)
  {
    $produksiId = $request->input('production_id');
    $produksi = Production::find($produksiId);

    if ($produksi) {
      return response()->json(['jumlah_produksi' => $produksi->jumlah_produksi]);
    }

    return response()->json(['error' => 'Produksi not found'], 404);
  }
  private function calculateDaysUntilExpiry($expiryDate)
  {
    $expiryDate = new DateTime($expiryDate);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($expiryDate);

    return $interval->days;
  }
  public function exportToExcel()
  {
    return Excel::download(new ProductsExport, 'products.xlsx');
  }
  public function generatePDF()
{
    // Dispatch job ke dalam job queue
    GenerateProductPdf::dispatch();

    // Memberikan respons kepada pengguna bahwa job telah dimulai
    return response()->json(['message' => 'Export PDF job has been started.']);
}


}
