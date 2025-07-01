<x-filament-panels::page>
    <form wire:submit="submit">
        {{-- Form filter tanggal kustom --}}
        {{ $this->form }}

        {{-- Grup Tombol --}}
        <div class="mt-6 flex flex-wrap gap-2 items-center">

            {{-- Tombol untuk menerapkan filter kustom --}}
            <x-filament::button type="submit">
                Terapkan Filter
            </x-filament::button>

            {{-- Tombol untuk filter cepat --}}
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

        </div>
    </form>
</x-filament-panels::page>