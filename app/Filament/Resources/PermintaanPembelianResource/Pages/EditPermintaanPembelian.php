<?php

namespace App\Filament\Resources\PermintaanPembelianResource\Pages;

use App\Filament\Resources\PermintaanPembelianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanPembelian extends EditRecord
{
    protected static string $resource = PermintaanPembelianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Menambahkan tombol Delete di pojok kanan atas halaman Edit
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Mengarahkan pengguna kembali ke halaman List (Tabel) setelah berhasil menyimpan perubahan.
     * Jika ini tidak ada, pengguna akan tetap di halaman Edit setelah simpan.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * (Opsional) Jika Anda ingin memodifikasi data sebelum disimpan
     * Tapi karena Resource sudah pakai ->relationship(), ini biasanya tidak perlu
     * kecuali ada logika khusus.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // $data['admin_id'] = auth()->id(); // Tidak perlu di-set ulang saat edit
        return $data;
    }
}
