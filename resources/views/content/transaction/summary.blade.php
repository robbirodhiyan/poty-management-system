@extends('layouts/contentNavbarLayout')

@section('title', 'Summary')

@section('content')

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Transaction /</span> Summary
        <ul class="list-inline mb-0 float-end">
            <li class="dropdown d-inline-block ">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="exportDropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Export
                </button>
                <div class="dropdown-menu" aria-labelledby="exportDropdown">
                    <a class="dropdown-item" href="{{ route('generate-pdf-summary', ['selectedMonth' => 'all']) }}">All Data</a>
                    @foreach ($availableMonths as $month)
                        <a class="dropdown-item" href="{{ route('generate-pdf-summary', ['selectedMonth' => $month]) }}">
                            {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                        </a>
                    @endforeach
                </div>
            </li>
        </ul>
    </h4>


    <!-- Basic Bootstrap Table -->
    <div class="card mb-5">
        {{-- <h5 class="card-header">Table Basic</h5> --}}
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-hover table-striped mb-0 bg-white" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>kategori</th>
                            <th>Sumber</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @php
                            $sortedTransactions = $transactions->sortBy('date');
                        @endphp
                        @foreach ($sortedTransactions as $transaction)
                            {{-- @dd($transaction) --}}
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $transaction->date }}</td>
                                <td>{{ $transaction->name }}</td>
                                <td>{{ $transaction->category }}</td>
                                <td>{{ $transaction->source }}</td>
                                <td>
                                    @if ($transaction instanceof App\Models\Debit)
                                        <span class="badge bg-success">Rp. {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($transaction instanceof App\Models\Credit)
                                        <span class="badge bg-danger">Rp. {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                            </tr>

                        @endforeach

                    </tbody>
                    <tfoot>

                        <tr>
                            <th colspan="5">Total</th>
                            <td>Rp. {{ number_format($transactions->whereInstanceOf('App\Models\Debit')->sum('total'), 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($transactions->whereInstanceOf('App\Models\Credit')->sum('total'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <br>
    <nav class="navbar rounded floating-nav">
        <div class="container-fluid">
            <h4 class="navbar-text center-y text-white">
              Saldo
            </h4>
            <h4 class="navbar-text center-y text-white">
              Rp.{{ number_format($transactions->whereInstanceOf('App\Models\Debit')->sum('total') - $transactions->whereInstanceOf('App\Models\Credit')->sum('total'), 0, ',', '.')}}
            </h4>
        </div>
    </nav>


@endsection

@push('css')
    <style>

        .floating-nav {
            position: fixed;
            bottom: 0;
            width: 79%;
            bottom: 20px;
            background-color: #112967;
            align-items: center;
            z-index: 1000; /* Ensure a higher z-index for the bottom navbar */
        }

        /* Add this style to adjust the spacing */
        .card.mb-5 {
            margin-bottom: 60px; /* Adjust this value as needed */
        }

    </style>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                // responsive: true,
                order: [
                    [0, 'desc']
                ],
                rowCallback: function(row, data, displayIndex, displayIndexFull) {
                    // Update the row number column
                    var api = this.api();
                    var index = api.row(displayIndexFull).index() + 1;
                    $('td:eq(0)', row).html(index);
                }
            });

        });
    </script>
@endpush
