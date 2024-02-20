<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class Credit extends Model
{
  use HasFactory;
  protected $guarded = ['id'];
  public function category()
  {
    return $this->belongsTo(Category::class);
  }
  public function source()
  {
    return $this->belongsTo(Source::class);
  }
  public function creditLog()
  {
    return $this->hasMany(CreditLog::class, 'credit', 'id');
  }

}
