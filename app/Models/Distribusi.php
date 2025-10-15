<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribusi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'agen_id');
    }


    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Pastikan juga relasi 'agen' ada untuk pemanggilan lain
     */
    public function agen()
    {
        return $this->belongsTo(User::class, 'agen_id');
    }
}
