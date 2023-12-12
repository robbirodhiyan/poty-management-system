<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['text', 'date', 'status']; // Tambahkan 'status' ke fillable
}
