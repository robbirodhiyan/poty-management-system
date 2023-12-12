<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;
  protected $fillable = ['nama_product', 'production_id', 'gambar','harga_jual', 'stok'];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }
}
