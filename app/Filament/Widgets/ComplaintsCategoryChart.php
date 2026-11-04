<?php

namespace App\Filament\Widgets;

use App\Models\ComplaintCategory;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class ComplaintsCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Complaints Distribution';

    protected ?string $description = 'Breakdown by technical category, urgency level, or resolution state.';

    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 1,
        'xl' => 1,
    ];

    protected ?string $maxHeight = '285px';

    public ?string $filter = 'category';

    protected function getFilters(): ?array
    {
        return [
            'category' => 'By Category',
            'priority' => 'By Priority',
            'status' => 'By Status',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? 'category';
        $user = auth()->user();

        $query = Ticket::query();
        if ($user) {
            $query->accessibleBy($user);
        }

        $labels = [];
        $data = [];
        $colors = [];

        if ($activeFilter === 'priority') {
            $priorities = [
                'critical' => ['label' => 'Critical Priority', 'color' => '#dc2626'],
                'high' => ['label' => 'High Priority', 'color' => '#ea580c'],
                'medium' => ['label' => 'Medium Priority', 'color' => '#f59e0b'],
                'low' => ['label' => 'Low Priority', 'color' => '#10b981'],
            ];

            foreach ($priorities as $key => $pInfo) {
                $count = (clone $query)->where('priority', $key)->count();
                if ($count > 0) {
                    $labels[] = $pInfo['label'];
                    $data[] = $count;
                    $colors[] = $pInfo['color'];
                }
            }
        } elseif ($activeFilter === 'status') {
            $statuses = [
                'submitted' => ['label' => 'Submitted / New', 'color' => '#3b82f6'],
                'under_review' => ['label' => 'Under Review', 'color' => '#6366f1'],
                'in_progress' => ['label' => 'Work In Progress', 'color' => '#0284c7'],
                'awaiting_confirmation' => ['label' => 'Awaiting User Confirmation', 'color' => '#8b5cf6'],
                'closed' => ['label' => 'Confirmed Closed', 'color' => '#059669'],
                'reopened' => ['label' => 'Reopened Issues', 'color' => '#ef4444'],
            ];

            foreach ($statuses as $key => $sInfo) {
                $count = (clone $query)->where('status', $key)->count();
                if ($count > 0) {
                    $labels[] = $sInfo['label'];
                    $data[] = $count;
                    $colors[] = $sInfo['color'];
                }
            }
        } else {
            // By Category
            $categories = ComplaintCategory::withCount(['tickets' => function ($q) use ($user) {
                if ($user) {
                    $q->accessibleBy($user);
                }
            }])
            ->having('tickets_count', '>', 0)
            ->orderByDesc('tickets_count')
            ->take(6)
            ->get();

            $palette = ['#059669', '#0284c7', '#8b5cf6', '#f59e0b', '#ec4899', '#14b8a6', '#64748b'];

            foreach ($categories as $index => $cat) {
                $labels[] = $cat->name;
                $data[] = $cat->tickets_count;
                $colors[] = $palette[$index % count($palette)];
            }
        }

        if (empty($data)) {
            $labels = ['No Active Tickets'];
            $data = [1];
            $colors = ['#cbd5e1'];
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

