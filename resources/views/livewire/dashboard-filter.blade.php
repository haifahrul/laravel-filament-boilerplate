<div class="mb-6 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dari
                Tanggal</label>
            <input type="date" wire:model.defer="from" id="from"
                class="w-full rounded-md shadow-sm border border-gray-300 bg-white text-gray-900
           focus:ring-primary-500 focus:border-primary-500
           dark:bg-gray-800 dark:border-gray-700 dark:text-white
           dark:placeholder-gray-400" />
        </div>

        <div>
            <label for="to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sampai
                Tanggal</label>
            <input type="date" wire:model.defer="to" id="to"
                class="w-full rounded-md shadow-sm border border-gray-300 bg-white text-gray-900
           focus:ring-primary-500 focus:border-primary-500
           dark:bg-gray-800 dark:border-gray-700 dark:text-white
           dark:placeholder-gray-400" />
        </div>

        <div>
            <label for="userId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sales</label>
            <select wire:model.defer="userId" id="userId"
                class="w-full rounded-md shadow-sm border border-gray-300 bg-white text-gray-900
           focus:ring-primary-500 focus:border-primary-500
           dark:bg-gray-800 dark:border-gray-700 dark:text-white
           dark:placeholder-gray-400">
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
