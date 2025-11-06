<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermintaanPembelian extends Model
{
    use HasFactory;
    protected $table = 'permintaan_pembelian';
    protected $fillable = ['admin_id', 'agen_id', 'tanggal_permintaan', 'status', 'catatan_admin'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function agen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agen_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPermintaanPembelian::class);
    }
}
