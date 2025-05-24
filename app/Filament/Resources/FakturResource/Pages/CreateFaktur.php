<?php

namespace App\Filament\Resources\FakturResource\Pages;

use App\Filament\Resources\FakturResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFaktur extends CreateRecord
{
    protected static string $resource = FakturResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total_final'] = ($data['total'] ?? 0) + ($data['charge'] ?? 0);
        return $data;
    }
}
