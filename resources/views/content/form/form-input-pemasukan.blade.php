@extends('layouts/contentNavbarLayout')

@section('title', 'Basic Inputs - Forms')

@section('page-script')
    <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
    <script src="https://cdn.rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        $(document).ready(function() {
            let scanner = new Instascan.Scanner({
                video: document.getElementById('scanner')
            });

            scanner.addListener('scan', function(content) {
                $('#product_qr_code').val(content);
                scanner.stop();
            });

            Instascan.Camera.getCameras().then(function(cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    console.error('No cameras found.');
                }
            });
            $('input[name="scanOption"]').change(function() {
                if ($(this).val() === 'qrCode') {
                    $('#scanner').show();
                    $('#product_qr_code').prop('readonly', true);
                    $('#product_qr_code-label').text('Scan QR Code Produk');
                    $('#stok').prop('readonly', true);
                    $('#stok').prop('hidden', false);
                    $('#stok').prop('disabled', false);
                    $('#total').prop('readonly', true);
                    $('#calculateTotal').prop('hidden', false);
                    // Mulai pemindai
                    scanner.start(cameras[0]);
                } else {
                    $('#scanner').hide();
                    $('#product_qr_code').prop('readonly', false);
                    $('#product_qr_code-label').text('Data Input');
                    $('#stok').prop('hidden', true);
                    $('#stok').prop('disabled', true);
                    $('#total').prop('readonly', false);
                    $('#calculateTotal').prop('hidden', true);
                    // Berhenti pemindai
                    scanner.stop();
                }
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Input /</span> Debit
    </h4>
    <div>
        <div class="card mb-4">
            <h5 class="card-header">Input Pemasukan</h5>
            <div class="card-body">
                <form action="{{ route('storedebit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="scanOption" class="form-label">Pilih Metode Input</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scanOption" id="qrCodeScan" value="qrCode"
                                checked>
                            <label class="form-check-label" for="qrCodeScan">Pemindaian Kode QR</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="scanOption" id="manualInput"
                                value="manualInput">
                            <label class="form-check-label" for="manualInput">Input Manual</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="scanner" class="form-label">Scan QR Code</label>
                        <br>
                        <video id="scanner" class="w-25"></video>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="product_qr_code" name="name"
                                    value="{{ old('product_qr_code') }}" placeholder="Scan QR Code Produk"
                                    aria-describedby="productQrCodeHelp" readonly/>
                                @error('product_qr_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <label id="product_qr_code-label" for="product_qr_code">Scan QR Code Produk</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="description" name="description"
                                    value="{{ old('description') }}" placeholder="Description"
                                    aria-describedby="floatingInputHelp" />
                                @error('description')
                                    <span class="text-danger"> {{ $message }} </span>
                                @enderror
                                <label for="floatingInput">Description</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="stok" name="stok"
                                    value="{{ old('stok') }}" placeholder="Rp. xxxxxx"
                                    aria-describedby="floatingInputHelp" readonly />
                                @error('stok')
                                    <span class="text-danger"> {{ $message }} </span>
                                @enderror
                                <label id="stok-label" for="stok">Sisa Stok</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="jumlah_item" name="jumlah_item"
                                    value="{{ old('jumlah_item') }}" placeholder="Rp. xxxxxx"
                                    aria-describedby="floatingInputHelp" />
                                @error('jumlah_item')
                                    <span class="text-danger"> {{ $message }} </span>
                                @enderror
                                <label for="floatingInput">Quantity</label>
                                <button type="button" class="btn btn-primary" id="calculateTotal">Calculate</button>
                            </div>
                            <input type="hidden" id="harga_jual" name="harga_jual"
                                value="{{ old('harga_jual') }} "disabled>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="total" name="total"
                                    value="{{ old('total') }}" placeholder="Rp. xxxxxx" aria-describedby="totalHelp"
                                    readonly />
                                @error('total')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <label for="total">Total</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-select" id="category" name="category">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->name }}"
                                            {{ old('category') == $category->name ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <span class="text-danger"> {{ $message }} </span>
                                @enderror
                                <label for="category">Kategory</label>
                            </div>
                            <div class="form-floating mb-3">

                                <select name="source" class="form-select" id="source">
                                    @foreach ($sources as $source)
                                        <option value="{{ $source->name }}"
                                            {{ old('source') == $source->name ? 'selected' : '' }}>
                                            {{ $source->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="source" class="form-label">Sumber</label>
                                @error('source')
                                    <span class="text-danger"> {{ $message }} </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="date" class="col-md-2 col-form-label">Date</label>

                                <input class="form-control" type="date" value="" id="date"
                                    name="date" />
                                @error('date')
                                    <span class="text-danger"> {{ $message }} </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6 d-grid">
                            <a href="{{ route('pemasukan') }}" class="btn btn-outline-dark btn-lg mt-3"><i
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
@endsection


@push('page-script')
    <script>
        $(document).ready(function() {
            let scanner = new Instascan.Scanner({
                video: document.getElementById('scanner')
            });

            scanner.addListener('scan', function(content) {
                // Set nilai input product_qr_code dengan hasil pemindaian QR Code
                $('#product_qr_code').val(content);

                // Panggil fungsi getProductData untuk mendapatkan data produk
                getProductData(content, function(productData) {
                    // Jika data produk ditemukan
                    if (productData) {
                        // Set nilai input description dengan nama produk
                        $('#description').val(productData.name_product);

                        // Set nilai input harga_jual (hidden) dengan harga jual produk
                        $('#harga_jual').val(productData.harga_jual);

                        // Set nilai input stok
                        $('#stok').val(productData.stok);

                        // Cek jika jumlah_item sudah terisi, lalu update total
                        if ($('#jumlah_item').val()) {
                            updateTotal();
                        }
                    } else {
                        // Jika data produk tidak ditemukan
                        // Handle sesuai kebutuhan aplikasi Anda
                        alert('Produk tidak ditemukan');
                        // Kosongkan nilai input description dan harga_jual
                        $('#description').val('');
                        $('#harga_jual').val('');
                        $('#stok').val('');
                        // Kosongkan total
                        $('#total').val('');
                    }
                });

                scanner.stop();
            });

            Instascan.Camera.getCameras().then(function(cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    console.error('No cameras found.');
                }
            });

            $('#calculateTotal').on('click', function() {
                updateTotal();
            });

            // Fungsi untuk mengambil data produk dari server
            function getProductData(productQrCode, callback) {
                $.ajax({
                    url: "{{ route('getProductData') }}",
                    type: 'GET',
                    data: {
                        name_product: productQrCode
                    },
                    dataType: 'json',
                    success: function(response) {
                        callback(response);
                    },
                    error: function(error) {
                        console.error('Error fetching product data:', error);
                        callback(null);
                    }
                });
            }

            function updateTotal() {
                var quantity = parseFloat($('#jumlah_item').val()) || 0;
                var hargaPerItem = parseFloat($('#harga_jual').val()) || 0;
                var total = quantity * hargaPerItem;

                $('#total').val(formatRupiah(total, 'Rp. '));

                var stok = parseFloat($('#stok').val()) || 0;
                console.log('Stok produk sebelum pemotongan: ' + stok);

                var newStok = stok - quantity;
                if (newStok < 0) {
                    alert('Stok tidak mencukupi');
                    $('#jumlah_item').val('');
                    $('#total').val('');
                    return;
                }

                console.log('Stok produk setelah pemotongan: ' + newStok);

                $('#stok').val(newStok);
            }

            // Fungsi untuk format angka menjadi format mata uang Rupiah
            function formatRupiah(angka, prefix) {
                var number_string = angka.toString().replace(/[^,\d]/g, '');
                var split = number_string.split(',');
                var sisa = split[0].length % 3;
                var rupiah = split[0].substr(0, sisa);
                var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
            }
        });
    </script>
@endpush
