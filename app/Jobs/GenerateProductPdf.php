<?php

namespace App\Jobs;

use App\Models\Product;

use Barryvdh\DomPDF\PDF;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateProductPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function handle()
    {
        // Ambil data produk dari model atau metode lainnya
        $products = Product::all();

        // Load view dan buat PDF
        $pdf = app('dompdf.wrapper')->loadView('pdf.product-pdf', compact('products'));

        // Simpan PDF ke penyimpanan
        $pdfPath = 'product_report.pdf';
        Storage::put($pdfPath, $pdf->output());
    }
}
