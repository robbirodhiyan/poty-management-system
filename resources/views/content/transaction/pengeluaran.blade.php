@extends('layouts/contentNavbarLayout')

@section('title', 'Pengeluaran')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">transaction /</span> Pengeluaran
        <ul class="list-inline mb-0 float-end">
            <li class="list-inline-item">
                <a href="#" class="btn btn-outline-primary">
                    <i class="bi bi-download me-1"></i> To Excel
                </a>
            </li>
            <li class="list-inline-item">|</li>
            <li class="list-inline-item">
                <a href="{{ route('InputCredit') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Input Data
                </a>
            </li>
        </ul>
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card p-4">
        <div class="table-responsive text-nowrap">
            <table class="table" id="datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>kategori</th>
                        <th>Sumber</th>
                        <th>Nominal</th>
                        <th>Log</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @php
                        $sortedTransactions = $credits->sortBy('date');
                    @endphp
                    @foreach ($sortedTransactions as $credit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $credit->date }}</td>
                            <td>{{ $credit->name }}</td>
                            <td>{{ $credit->category }}</td>
                            <td>{{ $credit->source }}</td>
                            <td>Rp. {{ number_format($credit->total, 0, ',', '.') }}</td>
                            <td>
                                @if ($credit->status_update == 0)
                                    <span class="badge bg-label-success me-1">Original</span>
                                @else
                                    <span class="badge bg-label-success me-1">Diperbarui</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-primary" onclick="window.location.href='{{ route('editpengeluaran', ['credit' => $credit->id]) }}'">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </button>
                                <button class="btn btn-info riwayat" data-name="{{ $credit->name }}" data-id="{{ $credit->id }}">
                                    <i class="bx bx-history me-1"></i> Lihat Riwayat
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->

    <!-- Modal -->
    <div class="modal fade" id="debitLog" tabindex="-1" aria-labelledby="debitLogLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="debitLogLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
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

        $(document).on('click', '.riwayat', function() {
        var id = $(this).val();
        var name = $(this).data('name');

        $.ajax({
            url: "{{ url('/auth/credit-log') }}/" + id,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $('#debitLogLabel').html(name);
                var html = '';
                var i;
                html += `
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Kategori</th>
                                    <th scope="col">Sumber</th>
                                    <th scope="col">Nominal</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                for (i = 0; i < data.length; i++) {
                    html += `
                        <tr>
                            <th scope="row">${data[i].created_at}</th>
                            <td>${data[i].name}</td>
                            <td>${data[i].category}</td>
                            <td>${data[i].source}</td>
                            <td>${data[i].total}</td>
                    `;

                    if (data[i].status_update == 0) {
                        html += `<td><span class="badge bg-label-success me-1">Original</span></td>`;
                    } else {
                        html += `<td><span class="badge bg-label-success me-1">Diperbarui</span></td>`;
                    }
                    html += `</tr>`;
                }

                html += `</tbody></table></div>`;
                $('.modal-body').html(html);
                $('#debitLog').modal('show');
            }
        });
    });
    </script>
@endpush
