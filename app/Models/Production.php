<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
  use HasFactory;
  protected $fillable = ['kode_produksi', 'mulai_produksi', 'exp_date', 'hpp', 'jumlah_produksi', 'product_id', 'nama_product'];

    public function product()
    {
        return $this->hasOne(Product::class, 'production_id');
    }
}
