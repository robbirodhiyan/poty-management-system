@extends('layouts/contentNavbarLayout')

@section('title', 'Produk')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"></span> Production
        <ul class="list-inline-item">
          <li class="dropdown d-inline-block ">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="exportDropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Export
            </button>
            <div class="dropdown-menu" aria-labelledby="exportDropdown">
                <a class="dropdown-item" href="{{ route('generate-pdf', ['selectedMonth' => 'all']) }}">All Data</a>
                @foreach($availableMonths as $month)
                    <a class="dropdown-item" href="{{ route('generate-pdf', ['selectedMonth' => $month]) }}">
                        {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                    </a>
                @endforeach
            </div>
        </li>
            {{-- <li class="list-inline-item">
                <a href="{{ route('generate-pdf') }}" class="btn btn-outline-primary">
                    <i class="bi bi-download me-1"></i> To PDF
                </a>
            </li> --}}
            <li class="list-inline-item">|</li>
            <li class="list-inline-item">
                <a href="{{ route('inputproduction') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Input Data
                </a>
            </li>
        </ul>
    </h4>

    <div class="card p-4">
        <div class="table-responsive text-nowrap">
            <table class="table" id="datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mulai Produksi</th>
                        <th>Kode Produksi</th>
                        <th>Nama Produk</th>
                        <th>Expired Date </th>
                        <th>Jumlah Produksi</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($productions as $key => $production)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $production->mulai_produksi }}</td>
                            <td>{{ $production->kode_produksi }}</td>
                            <td>{{ $production->nama_product }}</td>
                            <td>{{ date('d F Y', strtotime($production->exp_date)) }}</td>

                            <td>{{ $production->jumlah_produksi }}</td>
                            <td>
                                <button type="button" class="btn btn-primary"
                                    onclick="window.location='{{ route('editproduction', ['production' => $production->id]) }}'">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger delete-product" data-id="{{ $production->id }}"
                                    data-name="{{ $production->nama_product }}">
                                    <i class="bx bx-trash me-1"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('page-script')
    <script>
        // datatable
        $(document).ready(function() {
            $('#datatable').DataTable({
                responsive: true,
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

        $(document).on('click', '.delete-product', function() {
            var productId = $(this).data('id');
            var productName = $(this).data('name');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Produk ' + productName + ' akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika pengguna mengkonfirmasi, kirimkan permintaan DELETE ke rute deleteproduct
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('/deleteproduction') }}" + '/' + productId,
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            // Tampilkan Sweet Alert sukses
                            Swal.fire(
                                'Berhasil!',
                                'Produk ' + productName + ' telah dihapus.',
                                'success'
                            ).then((result) => {
                                // Muat ulang halaman setelah menghapus
                                location.reload();
                            });
                        },
                        error: function(data) {
                            // Tampilkan Sweet Alert gagal
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus produk.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
@endpush
