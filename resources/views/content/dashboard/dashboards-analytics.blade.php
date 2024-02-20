@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">


@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <style>
        .flatpickr-day.has-notes {
        background-color: #ffcccb;
        /* Ganti warna latar belakang sesuai kebutuhan Anda */
        color: #000;
        /* Ganti warna teks sesuai kebutuhan Anda */
        border-radius: 50%;
    }
    </style>

@endsection

@section('page-script')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.6/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.6/dist/flatpickr.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    var allNotes = {!! collect($notes)->toJson() !!};

    flatpickr('#openCalendar', {
        mode: 'single',
        dateFormat: 'Y-m-d',
        defaultDate: 'today',
        inline: true, // Menampilkan kalender secara tetap
        onReady: function(selectedDates, dateStr, instance) {
            highlightCalendarDates(selectedDates, allNotes);
        },
        onChange: function(selectedDates, dateStr, instance) {
            highlightCalendarDates(selectedDates, allNotes);
        }
    });

    function highlightCalendarDates(selectedDates, notes) {
      console.log('Selected Dates:', selectedDates);
    console.log('All Notes:', notes);
        // Menghapus tanda sebelumnya
        document.querySelectorAll('.flatpickr-day').forEach(function(day) {
            day.classList.remove('has-notes');
        });

        // Menandai tanggal dengan catatan
        notes.forEach(function(note) {
            var noteDate = new Date(note.date);
            var dateString = noteDate.toISOString().split('T')[0];

            if (selectedDates.includes(dateString)) {
                // Menambahkan kelas untuk menandai tanggal dengan catatan
                var calendarDay = document.querySelector(
                    '.flatpickr-day[data-date="' + dateString + '"]'
                );
                if (calendarDay) {
                    calendarDay.classList.add('has-notes');
                }
            }
        });
    }
});
        document.addEventListener('DOMContentLoaded', function() {
            var modalTriggerButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
            var transactionTypeInput = document.getElementById('transactionType');

            modalTriggerButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var transactionType = button.getAttribute('data-type');
                    transactionTypeInput.value = transactionType;
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
        // Cek apakah sweet alert harus ditampilkan
        @if(session('swal'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                timer: 1500,
                showConfirmButton: false
            });
        @endif
    });
    </script>

@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#datePickerModal" data-type="saldo">
                                    <i class='bx bx-dollar'></i>
                                </button>
                            </div>
                            @if ($selectedMonth)
                              <span class="fw-semibold d-block mb-1">Saldo <br>
                                {{ \Carbon\Carbon::parse(request('selectedMonth'))->format('F Y') }}</span>

                            <h6 class="card-title mb-2">Rp.
                                {{ number_format($transactions->whereInstanceOf('App\Models\Debit')->sum('total') - $transactions->whereInstanceOf('App\Models\Credit')->sum('total'), 0, ',', '.') }}
                            </h6>
                            @else
                              <span class="fw-semibold d-block mb-1">Saldo <br></span>
                            <h6 class="card-title mb-2">Rp.
                                {{ number_format($transactions->whereInstanceOf('App\Models\Debit')->sum('total') - $transactions->whereInstanceOf('App\Models\Credit')->sum('total'), 0, ',', '.') }}
                            </h6>
                            @endif
                            {{-- <small class="text-success fw-semibold"><i class='bx bx-up-arrow-alt'></i> +72.80%</small> --}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#datePickerModal" data-type="debit">
                                    <i class='bx bx-wallet'></i>
                                </button>
                            </div>
                            @if ($selectedMonth)
                              <span class="fw-semibold d-block mb-1">Debit <br>
                                {{ \Carbon\Carbon::parse(request('selectedMonth'))->format('F Y') }}</span>
                            <h6 class="card-title text-nowrap mb-1">Rp.
                                {{ number_format($transactions->whereInstanceOf('App\Models\Debit')->sum('total'), 0, ',', '.') }}
                            </h6>
                            @else
                              <span class="fw-semibold d-block mb-1">Debit <br></span>
                            <h6 class="card-title text-nowrap mb-1">Rp.
                                {{ number_format($transactions->whereInstanceOf('App\Models\Debit')->sum('total'), 0, ',', '.') }}
                            </h6>
                            @endif

                            {{-- <small class="text-success fw-semibold"><i class='bx bx-up-arrow-alt'></i> +28.42%</small> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
            <div class="row">
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#datePickerModal" data-type="kredit">
                                    <i class='bx bx-spreadsheet'></i>
                                </button>
                            </div>
                            @if ($selectedMonth)
                              <span class="fw-semibold d-block mb-1">Kredit <br>
                                {{ \Carbon\Carbon::parse(request('selectedMonth'))->format('F Y') }}</span>
                            <h6 class="card-title text-nowrap mb-2">Rp.
                                {{ number_format($transactions->whereInstanceOf('App\Models\Credit')->sum('total'), 0, ',', '.') }}
                            </h6>
                            @else
                              <span class="fw-semibold d-block mb-1">Kredit <br></span>
                            <h6 class="card-title text-nowrap mb-2">Rp.
                                {{ number_format($transactions->whereInstanceOf('App\Models\Credit')->sum('total'), 0, ',', '.') }}
                            </h6>
                            @endif

                            {{-- <small class="text-danger fw-semibold"><i class='bx bx-down-arrow-alt'></i> -14.82%</small> --}}
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="datePickerModal" tabindex="-1" aria-labelledby="datePickerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="datePickerModalLabel">Pilih Bulan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form untuk memilih bulan -->
                        <form action="{{ route('dashboard') }}" method="GET" class="p-3">
                            @csrf
                            <label for="selectedMonth" class="form-label">Pilih Bulan:</label>
                            <input type="hidden" id="transactionType" name="transactionType" value="">
                            <input type="month" class="form-control" id="selectedMonth" name="selectedMonth"
                                value="{{ request('selectedMonth') }}">
                            <button type="submit" class="btn btn-primary mt-3">Terapkan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                        class="rounded">
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Produk</span>
                            <h6 class="card-title mb-2">{{ $totalProducts }} Produk</h6>
                            {{-- <small class="text-success fw-semibold"><i class='bx bx-up-arrow-alt'></i> +28.14%</small> --}}
                        </div>
                    </div>
                </div>
                <!-- </div>
                                                                                                        <div class="row"> -->
            </div>
        </div>
        <div class="col-md-6 col-lg-4 order-3 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title m-0 me-2">Notes
                        <a href="{{ route('createNote') }}" class="btn btn-sm btn-success float-end">
                            <i class="bx bx-plus"></i>
                        </a>
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <button class="btn btn-primary" id="showAllNotesBtn" data-bs-toggle="modal"
                            data-bs-target="#allNotesModal">
                            <i class="bx bx-list-ul"></i> List Notes
                        </button>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-8 order-1 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Produk</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @php
                            // Urutkan produk berdasarkan stok dari yang terkecil ke terbesar
                            $sortedProducts = $products->sortBy('stok');
                        @endphp

                        @foreach ($sortedProducts as $product)
                            @php
                                // Tentukan kelas warna berdasarkan stok
                                $stokClass = $product->stok < 30 ? 'bg-danger' : 'bg-secondary';
                                // Tentukan teks peringatan
                                $peringatanStok = $product->stok < 30 ? 'Stok Kurang!' : '';
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $product->name_product }}</h6>
                                    <small class="text-muted">Available Stock: {{ $product->stok }}</small>
                                </div>
                                <!-- Ganti badge dengan tombol untuk menampilkan detail produk -->
                                <button type="button" class="badge {{ $stokClass }}" data-bs-toggle="modal"
                                    data-bs-target="#productDetailModal_{{ $product->id }}">
                                    {{ $peringatanStok }}
                                </button>
                            </li>

                            <!-- Modal untuk menampilkan detail produk -->
                            <div class="modal fade" id="productDetailModal_{{ $product->id }}" tabindex="-1"
                                aria-labelledby="productDetailModalLabel_{{ $product->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="productDetailModalLabel_{{ $product->id }}">
                                                Detail
                                                Produk: {{ $product->name_product }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Tambahkan konten detail produk di sini sesuai kebutuhan -->
                                            <p>Nama Produk: {{ $product->name_product }}</p>
                                            <p>Harga Jual: Rp. {{ $product->harga_jual }}</p>
                                            <p>Stok: {{ $product->stok }}</p>
                                            <!-- Tombol untuk menambah stok -->
                                            <a href="{{ route('inputproduction', ['name_product' => $product->name_product]) }}"
                                                class="btn btn-primary mt-2">Tambah Stok</a>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Tutup</button>
                                            <!-- Anda dapat menambahkan tombol aksi tambahan di sini jika diperlukan -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 order-3 mb-4">
            <div class="card h-40 w-100">
                <div class="card-header">
                    <h5 class="card-title m-0 me-2">Calendar</h5>
                </div>
                <div class="card-body">
                    <div class="flatpickr-calendar animate inline w-100 h-100" id="openCalendar" tabindex="-1"></div>
                </div>
            </div>
        </div>

         <div class="modal fade" id="allNotesModal" tabindex="-1" aria-labelledby="allNotesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allNotesModalLabel">All Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="allNotesContent">
                    @forelse($notes as $note)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-0">{{ $note->text }}</h6>
                                <small class="text-muted">Deadline: {{ $note->date }}</small>
                            </div>
                            @if (Auth::user() && Auth::user()->hasRole('owner'))
                                <!-- Show Edit and Delete buttons for users with the 'owner' role -->
                                <div class="ms-2">
                                    <a href="{{ route('editNote', $note->id) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                    <form action="{{ route('deleteNote', $note->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <hr>
                    @empty
                        <p>No notes available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @endsection
