<?php

namespace App\Filament\Resources\FakturResource\Pages;

use App\Filament\Resources\FakturResource;
use Filament\Resources\Pages\EditRecord;

class EditFaktur extends EditRecord
{
    protected static string $resource = FakturResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['total_final'] = ($data['total'] ?? 0) + ($data['charge'] ?? 0);
        return $data;
    }
}
