<?php

declare(strict_types=1);

namespace App\Filament\Resources\ImportResource\Pages;

use App\Filament\Resources\ImportResource;
use App\Models\ImportExport as ImportExportModel;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateImport extends CreateRecord
{
    protected static string $resource = ImportResource::class;

    protected static bool $canCreateAnother = false;

    protected $fillable = ['file'];

    protected function getHeaderWidgets(): array
    {
        return [
            ImportResource\Widgets\ImportExample::class,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file')
                    ->preserveFilenames()
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']),
            ]);
    }

    public function getTitle(): string
    {
        return __('imports.import_map_points_label');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ImportExportModel();
        $record->file = $data['file'];
        $record->type = 'map_points';
        $record->status = 0;
        $record->created_by = auth()->user()->id;
        $record->save();

        return $record;
    }
}
