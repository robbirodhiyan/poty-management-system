@extends('layouts/contentNavbarLayout')

@section('title', 'Basic Inputs - Forms')

@section('page-script')
    <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    // Dapatkan elemen input kode_produksi
    var kodeProduksiInput = document.getElementById('kode_produksi');
    var mulaiProduksiInput = document.getElementById('mulai_produksi');

    // Panggil fungsi untuk menghasilkan kode produksi saat halaman dimuat
    generateKodeProduksi();

    // Fungsi untuk menghasilkan kode produksi
    function generateKodeProduksi() {
        // Dapatkan data batch, day, month, dan year
        var batch = document.getElementById('batch').value;
        var mulaiProduksi = new Date(mulaiProduksiInput.value);

        if (isNaN(mulaiProduksi.getTime())) {
            // Jika tanggal tidak valid, keluar dari fungsi
            return;
        }

        var day = ("0" + mulaiProduksi.getDate()).slice(-2); // Tambahkan leading zero jika perlu
        var month = ("0" + (mulaiProduksi.getMonth() + 1)).slice(-2); // Tambahkan leading zero jika perlu
        var year = mulaiProduksi.getFullYear() % 100; // Ambil dua digit terakhir tahun

        // Format kode produksi: [batch]-[day]-[month]-[year]
        var kodeProduksi = batch + day + month + year;

        // Isi nilai kode produksi ke dalam input
        kodeProduksiInput.value = kodeProduksi;
    }

    // Tambahkan event listener untuk perubahan tanggal pada input mulai_produksi
    mulaiProduksiInput.addEventListener('change', function() {
        // Panggil kembali fungsi generateKodeProduksi saat tanggal berubah
        generateKodeProduksi();
    });
});
    </script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Input /</span> Produksi
</h4>
<div>
    <div class="card mb-4">
        <h5 class="card-header">Input Produksi</h5>
        <div class="card-body">
            <form action="{{ route('storeproduction') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="batch" name="batch" value="{{ old('batch') }}"
                        placeholder="Batch" aria-describedby="floatingInputHelp" />
                    @error('batch')
                        <span class="text-danger"> {{ $message }} </span>
                    @enderror
                    <label for="floatingInput">Batch</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="kode_produksi" name="kode_produksi" readonly
                        value="{{ old('kode_produksi') }}" placeholder="Description"
                        aria-describedby="floatingInputHelp" />
                    @error('kode_produksi')
                        <span class="text-danger"> {{ $message }} </span>
                    @enderror
                    <label for="floatingInput">Kode Produksi</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nama_product" name="nama_product"
                        value="{{ old('nama_product') ?? $name_product }}" placeholder="Product" aria-describedby="floatingInputHelp" />
                    @error('nama_product')
                        <span class="text-danger"> {{ $message }} </span>
                    @enderror
                    <label for="floatingInput">Nama Produk</label>
                </div>
                <div class="mb-3">
                    <label for="date" class="col-md-2 col-form-label">Mulai Produksi</label>

                    <input class="form-control" type="date" value="" id="mulai_produksi"
                        name="mulai_produksi" />
                    @error('mulai_produksi')
                        <span class="text-danger"> {{ $message }} </span>
                    @enderror

                </div>
                <div class="mb-3">
                    <label for="date" class="col-md-2 col-form-label">Expired Date</label>

                    <input class="form-control" type="date" value="" id="exp_date" name="exp_date" />
                    @error('exp_date')
                        <span class="text-danger"> {{ $message }} </span>
                    @enderror

                </div>


                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="jumlah_produksi" name="jumlah_produksi"
                        value="{{ old('jumlah_produksi') }}" placeholder="XXXXX"
                        aria-describedby="floatingInputHelp" />
                    @error('jumlah_produksi')
                        <span class="text-danger"> {{ $message }} </span>
                    @enderror
                    <label for="floatingInput">Jumlah Produksi</label>

                </div>

                <div class="row">
                    <div class="col-md-6 d-grid">
                        <a href="{{ route('production') }}" class="btn btn-outline-dark btn-lg mt-3"><i
                                class="bi-arrow-left-circle me-2"></i> Cancel</a>
                    </div>
                    <div class="col-md-6 d-grid">
                        <button type="submit" class="btn btn-dark btn-lg mt-3"><i
                                class="bi-check-circle me-2"></i>Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection


@push('page-script')
<script>
    var dengan_rupiah = document.getElementById('total');
    dengan_rupiah.addEventListener('keyup', function(e) {
        dengan_rupiah.value = formatRupiah(this.value, 'Rp. ');
    });

    /* Fungsi */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
    }
</script>
@endpush
