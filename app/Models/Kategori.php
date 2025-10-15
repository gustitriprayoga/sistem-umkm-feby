<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;
    protected $fillable = ['nama_kategori', 'deskripsi'];


    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_kategori', 'kategori_id', 'user_id');
    }
}
