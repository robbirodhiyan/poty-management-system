<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        /* Tambahkan gaya styling sesuai kebutuhan Anda */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Products PDF</h1>

    <table border="1">
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
                            <img src="{{ asset('storage/' . $product->gambar) }}" alt="Gambar Produk" width="100">
                        @else
                            Tidak Ada Gambar
                        @endif
                    </td>
                    <td>
                        {!! QrCode::size(80)->generate($product->name_product) !!}
                    </td>
                    <td>{{ $product->kode_produksi }}</td>
                    <td>{{ $product->name_product }}</td>
                    <td>{{ $product->days_until_expiry }} hari lagi </td>
                    <td>Rp. {{ $product->harga_jual }}</td>
                    <td>{{ $product->stok }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>
