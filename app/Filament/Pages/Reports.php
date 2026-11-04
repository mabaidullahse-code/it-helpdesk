<?php

namespace App\Filament\Pages;

use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\User;
use App\Services\ReportExportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Reports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static UnitEnum|string|null $navigationGroup = 'Complaints & Support Tickets';

    protected static ?string $navigationLabel = 'Reports & Analytics';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.reports';

    public ?array $filters = [
        'from_date' => null,
        'to_date' => null,
        'department_id' => null,
        'category_id' => null,
        'status' => null,
        'priority' => null,
        'assigned_technician_id' => null,
    ];

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isItStaff() || $user->isDepartmentHod());
    }

    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        $this->form->fill([
            'from_date' => now()->startOfMonth()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
            'department_id' => ($user && $user->isDepartmentHod() && !$user->isItHod()) ? $user->department_id : null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        $isRestrictedHod = $user && $user->isDepartmentHod() && !$user->isItHod();

        return $schema
            ->components([
                Section::make('Report Criteria & Data Filters')
                    ->description('Filter complaints by timeline, department, priority, and operational status.')
                    ->schema([
                        Grid::make(4)->schema([
                            DatePicker::make('from_date')
                                ->label('From Date'),
                            DatePicker::make('to_date')
                                ->label('To Date'),
                            Select::make('department_id')
                                ->label('Department')
                                ->options(Department::where('is_active', true)->pluck('name', 'id'))
                                ->disabled($isRestrictedHod)
                                ->searchable()
                                ->nullable(),
                            Select::make('category_id')
                                ->label('Category')
                                ->options(ComplaintCategory::where('is_active', true)->pluck('name', 'id'))
                                ->searchable()
                                ->nullable(),
                            Select::make('status')
                                ->label('Ticket Status')
                                ->options([
                                    'submitted' => 'Submitted',
                                    'under_review' => 'Under Review',
                                    'assigned' => 'Assigned',
                                    'in_progress' => 'In Progress',
                                    'awaiting_information' => 'Awaiting Information',
                                    'on_hold' => 'On Hold',
                                    'awaiting_confirmation' => 'Awaiting Confirmation',
                                    'reopened' => 'Reopened',
                                    'escalated' => 'Escalated',
                                    'closed' => 'Closed',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->nullable(),
                            Select::make('priority')
                                ->label('Priority')
                                ->options([
                                    'critical' => 'Critical',
                                    'high' => 'High',
                                    'medium' => 'Medium',
                                    'low' => 'Low',
                                ])
                                ->nullable(),
                            Select::make('assigned_technician_id')
                                ->label('Technician')
                                ->options(User::where('role', 'it_technician')->pluck('name', 'id'))
                                ->searchable()
                                ->nullable(),
                        ]),
                    ])
            ])
            ->statePath('filters');
    }

    public function applyFilters(): void
    {
        // Reactive hook to refresh stats
    }

    public function getStatsProperty(): array
    {
        $query = app(ReportExportService::class)->getFilteredQuery($this->filters ?? [], auth()->user());

        $total = (clone $query)->count();
        $open = (clone $query)->whereNotIn('status', ['closed', 'cancelled'])->count();
        $closed = (clone $query)->where('status', 'closed')->count();
        $reopened = (clone $query)->where('status', 'reopened')->count();
        $overdue = (clone $query)->overdue()->count();
        $confirmation = (clone $query)->where('status', 'awaiting_confirmation')->count();

        $rate = $total > 0 ? round(($closed / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'open' => $open,
            'closed' => $closed,
            'reopened' => $reopened,
            'overdue' => $overdue,
            'confirmation' => $confirmation,
            'rate' => $rate,
        ];
    }

    public function getRecentTicketsProperty()
    {
        return app(ReportExportService::class)->getFilteredQuery($this->filters ?? [], auth()->user())
            ->take(15)
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export Official PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    return app(ReportExportService::class)->exportPdf($this->filters ?? [], auth()->user());
                }),

            Action::make('export_excel')
                ->label('Export Excel (.xlsx)')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->action(function () {
                    return app(ReportExportService::class)->exportExcel($this->filters ?? [], auth()->user());
                }),

            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return app(ReportExportService::class)->exportCsv($this->filters ?? [], auth()->user());
                }),
        ];
    }
}
