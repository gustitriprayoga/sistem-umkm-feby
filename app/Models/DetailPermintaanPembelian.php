<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPermintaanPembelian extends Model
{
    use HasFactory;
    protected $table = 'detail_permintaan_pembelian';
    protected $fillable = ['permintaan_pembelian_id', 'nama_barang', 'jumlah', 'satuan'];

    public function permintaanPembelian(): BelongsTo
    {
        return $this->belongsTo(PermintaanPembelian::class);
    }
}
