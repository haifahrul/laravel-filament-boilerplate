<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DashboardOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard-overview';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -1;
}