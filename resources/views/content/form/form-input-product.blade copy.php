@extends('layouts/contentNavbarLayout')

@section('title', 'Basic Inputs - Forms')

@section('page-script')
    <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Input /</span> Product
    </h4>
    <div>
        <div class="card mb-4">
            <h5 class="card-header">Input Product</h5>
            <div class="card-body">
                <form action="{{ route('storeproduct') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-floating mb-3">
                        <select class="form-control" id="name_product" name="name_product"
                            aria-describedby="floatingInputHelp">
                            @foreach ($productions as $production)
                                <option value="{{ $production->nama_product }}"
                                    {{ old('name_product') == $production->nama_product ? 'selected' : '' }}>
                                    {{ $production->kode_produksi . ' - ' . $production->nama_product }}
                                </option>
                            @endforeach
                        </select>
                        @error('name_product')
                            <span class="text-danger"> {{ $message }} </span>
                        @enderror
                        <label for="floatingInput">Nama Produk</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-control" id="stok" name="stok" aria-describedby="floatingInputHelp">
                            @foreach ($productions as $production)
                                <option value="{{ $production->jumlah_produksi }}"
                                    {{ old('production') == $production->id ? 'selected' : '' }}>
                                    {{ $production->jumlah_produksi }}
                                </option>
                            @endforeach
                        </select>
                        @error('stok')
                            <span class="text-danger"> {{ $message }} </span>
                        @enderror
                        <label for="floatingInput">Stok Masuk</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="harga_jual" name="harga_jual"
                            value="{{ old('harga_jual') }}" placeholder="Rp. xxx.xxx"
                            aria-describedby="floatingInputHelp" />
                        @error('harga_jual')
                            <span class="text-danger"> {{ $message }} </span>
                        @enderror
                        <label for="floatingInput">Harga Jual</label>
                    </div>


                    <div class="mb-3">
                        <label for="file" class="form-label">Gambar</label>
                        <input class="form-control" type="file" id="gambar" name="gambar"
                            accept="image/jpeg, image/jpg, image/png">
                        @error('gambar')
                            <span class="text-danger"> {{ $message }} </span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 d-grid">
                            <a href="{{ route('product') }}" class="btn btn-outline-dark btn-lg mt-3"><i
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
