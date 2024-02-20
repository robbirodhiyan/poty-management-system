@extends('layouts/contentNavbarLayout')

@section('title', 'Kategori')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Lainnya /</span> Kategori
        <ul class="list-inline mb-0 float-end">
            <li class="list-inline-item">
                <a href="{{ route('InputKategori') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Input Data
                </a>
            </li>
        </ul>
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card p-4">
        {{-- <h5 class="card-header">Table Basic</h5> --}}
        <div class="table-responsive text-nowrap">
            <table class="table" id="datatables">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($categories as $key => $category)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $category->name }}</td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->

@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
          responsive: true,
            // Inisialisasi DataTables
            $('#datatables').DataTable();
        });
    </script>
@endsection
