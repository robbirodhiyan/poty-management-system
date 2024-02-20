<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .letterhead {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 20px;
        }

        .letterhead img {
            max-width: 150px;
            /* Sesuaikan lebar logo sesuai kebutuhan */
            height: auto;
            margin-right: 20px;
            /* Jarak antara logo dan teks */
        }

        .letterhead h1 {
            margin: 0;
            font-size: 24px;
        }

        .letterhead p {
            margin: 5px 0 0;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .divider {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>

<body>

    <div class="letterhead">
        <img src='assets/img/potylogohorizontal2.png' alt="Logo Perusahaan">
        <div>
            <h1>CV Poty Teknologi Pertanian</h1>
            <p>Jl. KH. Malik Dalam, Buring, Kec. Kedungkandang, Kota Malang, Jawa Timur 65136</p>
            <p>No. Telp: 0857-0617-0791</p>
        </div>
        <div class="divider"></div>
    </div>
    <h1 style="text-align: center; font-size: 28px; margin-bottom: 20px;">Laporan Keuangan</h1>
    <p style="text-align: center; font-size: 16px; margin-bottom: 20px;">
        Periode:
        {{ $selectedMonth ? (\Carbon\Carbon::hasFormat($selectedMonth, 'Y-m') ? \Carbon\Carbon::parse($selectedMonth)->format('F Y') : 'Semua Waktu') : 'Semua Waktu' }}
    </p>
    <div class="card mb-5">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped mb-0 bg-white" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Sumber</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sortedTransactions = $mergedData->sortByDesc('date');
                        @endphp
                        @foreach ($sortedTransactions as $transaction)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $transaction->date }}</td>
                                <td>{{ $transaction->name }}</td>
                                <td>{{ $transaction->category }}</td>
                                <td>{{ $transaction->source }}</td>
                                <td>
                                    @if ($transaction instanceof App\Models\Debit)
                                        <span class="badge bg-success">Rp.
                                            {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($transaction instanceof App\Models\Credit)
                                        <span class="badge bg-danger">Rp.
                                            {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">Total</th>
                            <td>Rp.
                                {{ number_format($sortedTransactions->whereInstanceOf('App\Models\Debit')->sum('total'), 0, ',', '.') }}
                            </td>
                            <td>Rp.
                                {{ number_format($sortedTransactions->whereInstanceOf('App\Models\Credit')->sum('total'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
