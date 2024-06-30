<?php

namespace App\Filament\Resources\ProductivityResource\Pages;

use App\Filament\Resources\ProductivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductivities extends ListRecords
{
    protected static string $resource = ProductivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
