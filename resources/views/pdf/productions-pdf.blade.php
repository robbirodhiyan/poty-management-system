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
      background-color: #f4f4f4;
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

    .table th, .table td {
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
  </style>
</head>
<body>
  <div class="card">
    <div class="table-responsive text-nowrap">
      <table class="table" id="datatable">
        <thead>
          <tr>
            <th>No</th>
            <th>Mulai Produksi</th>
            <th>Kode Produksi</th>
            <th>Nama Produk</th>
            <th>Expired Date</th>
            <th>Jumlah Produksi</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @foreach ($productions as $key => $production)
            <tr>
              <td>{{ $key + 1 }}</td>
              <td>{{ date('d F Y', strtotime($production->mulai_produksi)) }}</td>
              <td>{{ $production->kode_produksi }}</td>
              <td>{{ $production->nama_product }}</td>
              <td>{{ date('d F Y', strtotime($production->exp_date)) }}</td>
              <td>{{ $production->jumlah_produksi }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
