<div
    class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="grid gap-y-2">
        <div class="flex items-center gap-x-2">
            <span class="fi-wi-stats-overview-stat-label text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $label }}
            </span>
        </div>

        <div class="fi-wi-stats-overview-stat-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
            {{ $value }}
        </div>

        @if ($description)
            <div class="flex items-center gap-x-1">
                <span
                    class="fi-wi-stats-overview-stat-description text-sm fi-color-custom text-custom-600 fi-color-{{ $color }} text-{{ $color }}-600 dark:text-{{ $color }}-400"
                    style="--c-400:var(--{{ $color }}-400);--c-600:var(--{{ $color }}-600);">
                    {{ $description }}
                </span>
            </div>
        @endif
    </div>
</div>
