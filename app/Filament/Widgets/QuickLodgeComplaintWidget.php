<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickLodgeComplaintWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-lodge-complaint-widget';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'sm' => 'full',
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    protected static bool $isLazy = false;

    protected static ?int $sort = -10;
}
