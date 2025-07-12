<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    @include('components.stats-card', [
        'label' => 'Total Order',
        'value' => $totalOrders,
        'description' => 'Hari ini',
        'color' => 'success',
    ])

    @include('components.stats-card', [
        'label' => 'Kunjungan Hari Ini',
        'value' => $totalVisits,
        'description' => 'Sales Visit',
        'color' => 'primary',
    ])

    @include('components.stats-card', [
        'label' => 'Sales Aktif',
        'value' => $activeSales,
        'description' => 'Dengan aktivitas hari ini',
        'color' => 'info',
    ])
</div>
