<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
  <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"></span> Product
    </h4>


    <!-- Basic Bootstrap Table -->
    <div class="card p-4">
        {{-- <h5 class="card-header">Table Basic</h5> --}}
        <div class="table-responsive text-nowrap">
            <table class="table" id="datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Kode Produk</th>
                        <th>Kode Produksi</th>
                        <th>Nama Produk</th>
                        <th>Expired Date</th>
                        <th>Harga Jual</th>
                        <th>Sisa Stock</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($products as $key => $product)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if ($product->gambar)
                                    <img src="{{ asset('storage/' . $product->gambar) }}" alt="Gambar Produk"
                                        width="100">
                                @else
                                    Tidak Ada Gambar
                                @endif
                            </td>
                            <td>
                                {!! QrCode::size(80)->generate($product->name_product) !!}
                            </td>
                            <td>{{ $product->kode_produksi }}</td>
                            <td>{{ $product->name_product }}</td>
                            <td>{{ date('d F Y', strtotime($product->exp_date)) }} </td>
                            <td>Rp. {{ $product->harga_jual }}</td>
                            <td>{{ $product->stok }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
