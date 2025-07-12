<div class="space-y-4">
    @if ($old)
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Data Sebelumnya</h3>
            <ul class="text-sm text-gray-600 dark:text-gray-400">
                @foreach ($old as $key => $value)
                    <li><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($new)
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Data Sekarang</h3>
            <ul class="text-sm text-gray-600 dark:text-gray-400">
                @foreach ($new as $key => $value)
                    <li><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (! $old && ! $new)
        <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada perubahan data yang tercatat.</p>
    @endif
</div>
