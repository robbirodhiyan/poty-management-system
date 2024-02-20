<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditLog extends Model
{
    use HasFactory;
    protected $table = 'creditlog';

    protected $guarded = ['id'];
    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }
}
