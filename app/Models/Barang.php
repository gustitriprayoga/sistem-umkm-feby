<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Barang extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    use HasFactory;
    protected $fillable = ['nama_barang', 'kategori_id', 'deskripsi', 'stok', 'harga'];

    /**
     * [2] INI ADALAH RELASI YANG HILANG.
     * Mendefinisikan bahwa satu Barang "milik" satu Kategori.
     * Nama method 'kategori' ini HARUS SAMA PERSIS dengan yang dipanggil di ->relationship().
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function distribusis(): HasMany
    {
        return $this->hasMany(Distribusi::class, 'barang_id');
    }

    public function latestDistribusi(): HasOne
    {
        return $this->hasOne(Distribusi::class)->latestOfMany();
    }
}
