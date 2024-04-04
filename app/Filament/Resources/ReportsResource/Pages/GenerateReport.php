<?php

declare(strict_types=1);

namespace App\Filament\Resources\ReportsResource\Pages;

use App\Contracts\Pages\WithTabs;
use App\Filament\Resources\ReportsResource;
use App\Filament\Resources\ReportsResource\Concerns;
use App\Filament\Resources\ReportsResource\Traits\ReportsTrait;
use App\Models\Report;
use Filament\Actions\Action;
use Filament\Actions\Action as FormAction;
// use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class GenerateReport extends Page implements HasForms, WithTabs, HasTable, HasActions
{
    use Concerns\HasTabs;
    use InteractsWithFormActions;
    use InteractsWithTable;
    use ReportsTrait;

    public ?array $data = [];

    public ?Report $record = null;

    public $isGrouped = false;

    public $groupedBy = null;

    public $shouldGenerate = false;

    protected static string $resource = ReportsResource::class;

    protected static string $view = 'filament.resources.reports-resource.pages.generate';

    protected static bool $canCreateAnother = false;

    public $type = 'map_points';

    public function mount(): void
    {
        $this->authorizeAccess();
        $this->type = request()->get('type', 'map_points');
        abort_unless(\in_array($this->type, ['map_points', 'issues', 'users']), 403);

        $this->data['should_generate'] = false;
        $this->data['is_grouped'] = false;
        $this->data['type'] = $this->type;
        $this->fillForm();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_list')->label(__('report.action.view_list'))->url('/admin/reports'),
        ];
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::canCreate(), 403);
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');
        $this->form->fill();
        $this->callHook('afterFill');
    }

    public function generate()
    {
        $this->authorizeAccess();

        $this->type = request()->get('type', 'map_points');
        try {
            $this->data = $this->form->getState();
            $this->shouldGenerate = true;
        } catch (Halt $exception) {
            return;
        }
    }

    protected function getForms(): array
    {
        return [
            'form',
            // 'report',
        ];
    }

    public function updateGoupedBy($field)
    {
        $this->groupedBy = $field;
        $this->shouldGenerate = false;
    }

    protected function getFormActions(): array
    {
        return [
            FormAction::make('create')
                ->label(__('report.action.generate'))
                ->keyBindings(['mod+s'])
                ->color('success')
                ->action(function ($data) {
                    return $this->generate();
                }),
            FormAction::make('cancel')
                ->label(__('report.action.reset'))
                ->url(static::getResource()::getUrl())
                ->color('primary'),

        ];
    }

    public function getTableColumns(): array
    {
        return $this->getReportTableColumns();
    }

    public function table(Table $table): Table
    {
        return $this->getReportTable($table);
    }
}
