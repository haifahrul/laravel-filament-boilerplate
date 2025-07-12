<div
    class="fi-wi-stats-overview-stat relative rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
        Produk Terlaris Mingguan
    </h3>

    <ul class="space-y-1 max-h-[200px] overflow-y-auto pr-1">
        @forelse ($topProducts as $item)
            <li
                class="flex justify-between items-center py-2 px-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors">
                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">
                    {{ $item->product->name ?? 'Produk dihapus' }}
                </span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $item->total_sold }} pcs
                </span>
            </li>
        @empty
            <li class="py-2 px-2 text-sm text-gray-500 dark:text-gray-400">
                Tidak ada data.
            </li>
        @endforelse
    </ul>
</div>
