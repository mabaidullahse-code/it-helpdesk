<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ComplaintsTrendChart extends ChartWidget
{
    protected ?string $heading = 'Complaints Lodging & Resolution Trend';

    protected ?string $description = 'Daily and monthly trend comparison of incoming complaints versus resolved tickets.';

    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 1,
        'xl' => 1,
    ];

    protected ?string $maxHeight = '285px';

    public ?string $filter = '30days';

    protected function getFilters(): ?array
    {
        return [
            '7days' => 'Last 7 Days',
            '30days' => 'Last 30 Days',
            '6months' => 'Last 6 Months',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? '30days';
        $user = auth()->user();

        $ticketQuery = Ticket::query();
        if ($user) {
            $ticketQuery->accessibleBy($user);
        }

        $labels = [];
        $lodgedCounts = [];
        $resolvedCounts = [];

        if ($activeFilter === '7days') {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('D, M j');

                $lodgedCounts[] = (clone $ticketQuery)
                    ->whereDate('created_at', $date)
                    ->count();

                $resolvedCounts[] = (clone $ticketQuery)
                    ->whereIn('status', ['closed', 'confirmed_resolved'])
                    ->whereDate('completed_at', $date)
                    ->count();
            }
        } elseif ($activeFilter === '6months') {
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::today()->subMonths($i);
                $labels[] = $month->format('M Y');

                $lodgedCounts[] = (clone $ticketQuery)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                $resolvedCounts[] = (clone $ticketQuery)
                    ->whereIn('status', ['closed', 'confirmed_resolved'])
                    ->whereYear('completed_at', $month->year)
                    ->whereMonth('completed_at', $month->month)
                    ->count();
            }
        } else {
            // Default: 30 days grouped every 3 days (10 points)
            for ($i = 9; $i >= 0; $i--) {
                $startDate = Carbon::today()->subDays(($i + 1) * 3 - 1)->startOfDay();
                $endDate = Carbon::today()->subDays($i * 3)->endOfDay();
                $labels[] = $endDate->format('M j');

                $lodgedCounts[] = (clone $ticketQuery)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $resolvedCounts[] = (clone $ticketQuery)
                    ->whereIn('status', ['closed', 'confirmed_resolved'])
                    ->whereBetween('completed_at', [$startDate, $endDate])
                    ->count();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Complaints Lodged',
                    'data' => $lodgedCounts,
                    'borderColor' => '#059669',
                    'backgroundColor' => 'rgba(5, 150, 105, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#059669',
                    'pointBorderColor' => '#ffffff',
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Tickets Resolved',
                    'data' => $resolvedCounts,
                    'borderColor' => '#0284c7',
                    'backgroundColor' => 'rgba(2, 132, 199, 0.1)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#0284c7',
                    'pointBorderColor' => '#ffffff',
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

