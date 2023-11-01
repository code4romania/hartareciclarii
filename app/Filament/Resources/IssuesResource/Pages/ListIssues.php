<?php

namespace App\Filament\Resources\IssuesResource\Pages;

use App\Contracts\Pages\WithTabs;
use App\Filament\Resources\IssuesResource;
use App\Filament\Resources\IssuesResource\Concerns;
use App\Models\Issue as IssueModel;
use Filament\Actions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ListIssues extends Page implements WithTabs, HasForms, HasTable, HasActions
{
    use Concerns\HasTabs;

    use InteractsWithFormActions;

    use InteractsWithTable;

    public ?array $data = [];

    protected static string $resource = IssuesResource::class;

    protected static string $view = 'filament.resources.issues-resource.pages.list';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        $data = $this->data;
        $actions = [];

        return $table
            ->query(function ()
            {
                return IssueModel::query();
            })
            ->headerActions(
                $actions,
            )
            ->paginated(false)
            ->columns($this->getTableColumns())
            ->view('filament.resources.reports-resource.pages.view', [
                'data'=>$this->data,
                'header'=>$this->getTableColumns(),
            ]);
        // ->view('filament-tables::index');
    }
}
