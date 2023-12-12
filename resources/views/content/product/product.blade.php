@extends('layouts/contentNavbarLayout')

@section('title', 'Produk')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"></span> Product
        <ul class="list-inline mb-0 float-end">
            <li class="list-inline-item">

                <a href="{{ route('generate-pdf-product') }}" class="btn btn-outline-primary">
                    <i class="bi bi-download me-1"></i> To PDF
                </a>
            </li>
            <li class="list-inline-item">|</li>
            <li class="list-inline-item">
                <a href="{{ route('inputproduct') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Input Data
                </a>
            </li>
        </ul>
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
                        <th>Action</th>
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
                            <td>{{ $product->exp_date }} </td>
                            <td>Rp. {{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                            <td>{{ $product->stok }}</td>

                            <td>
                                <button type="button" class="btn btn-primary"
                                    onclick="window.location='{{ route('editproduct', ['product' => $product->id]) }}'">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger delete-production"
                                    data-id="{{ $product->id }}" data-name="{{ $product->name_product }}">
                                    <i class="bx bx-trash me-1"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->
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

        $(document).on('click', '.delete-production', function() {
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
                        url: "{{ url('/deleteproduct') }}" + '/' + productId,
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
