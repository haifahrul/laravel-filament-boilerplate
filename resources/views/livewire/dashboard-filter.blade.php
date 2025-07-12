<div class="mb-6 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="from" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
            <input type="date" wire:model.defer="from" id="from"
                class="w-full border-gray-300 rounded-md shadow-sm" />
        </div>

        <div>
            <label for="to" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
            <input type="date" wire:model.defer="to" id="to"
                class="w-full border-gray-300 rounded-md shadow-sm" />
        </div>

        <div>
            <label for="userId" class="block text-sm font-medium text-gray-700">Sales</label>
            <select wire:model.defer="userId" id="userId"
                class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="">-- Semua Sales --</option>
                @foreach ($salesList as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <x-filament::button color="gray" wire:click="resetFilter" type="button">
            Reset
        </x-filament::button>

        <x-filament::button wire:click="applyFilter" type="button">
            Terapkan
        </x-filament::button>
    </div>
</div>
