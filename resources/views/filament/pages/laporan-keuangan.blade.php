<x-filament-panels::page>

    {{-- Menampilkan form date picker --}}
    <div class="mb-4">
        {{ $this->form }}
    </div>

    {{-- Menampilkan tombol-tombol filter cepat --}}
    <div class="flex items-center gap-2 mb-6">
        <x-filament::button wire:click="setFilterHarian" color="gray">
            Hari Ini
        </x-filament::button>

        <x-filament::button wire:click="setFilterMingguan" color="gray">
            Minggu Ini
        </x-filament::button>

        <x-filament::button wire:click="setFilterBulanan" color="gray">
            Bulan Ini
        </x-filament::button>

        <x-filament::button wire:click="setFilterTahunan" color="gray">
            Tahun Ini
        </x-filament::button>

        <x-filament::button wire:click="setFilterSatuTahunTerakhir" color="gray">
            1 Tahun Terakhir
        </x-filament::button>
    </div>

    {{--
        Widget akan otomatis ditampilkan oleh Filament di sini,
        jadi Anda tidak perlu memanggilnya secara manual di view.
    --}}

</x-filament-panels::page>
