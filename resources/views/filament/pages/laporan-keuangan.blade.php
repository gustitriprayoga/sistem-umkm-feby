<x-filament-panels::page>
    {{-- Tampilkan form filter tanggal --}}
    <div class="mb-4">
        {{ $this->form }}
    </div>

    {{-- [2] KONTENER UNTUK TOMBOL-TOMBOL FILTER CEPAT --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Filter Cepat:</span>

        <x-filament::button wire:click="setFilterHarian" color="gray" size="sm">
            Hari Ini
        </x-filament::button>

        <x-filament::button wire:click="setFilterMingguan" color="gray" size="sm">
            Minggu Ini
        </x-filament::button>

        <x-filament::button wire:click="setFilterBulanan" color="gray" size="sm">
            Bulan Ini
        </x-filament::button>

        <x-filament::button wire:click="setFilterTahunan" color="gray" size="sm">
            Tahun Ini
        </x-filament::button>
    </div>

    <div class="prose dark:prose-invert max-w-none">
        <p>
            Gunakan filter di atas untuk melihat ringkasan keuangan dan rincian transaksi pada periode yang Anda pilih.
        </p>
    </div>

</x-filament-panels::page>
