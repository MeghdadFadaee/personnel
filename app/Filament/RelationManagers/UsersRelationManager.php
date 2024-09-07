<?php

namespace App\Filament\RelationManagers;

use Illuminate\Contracts\View\View;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('username')
            ->columns([
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('username'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->modalHeading($this->getAttachHeading())
                    ->stickyModalHeader()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return trans(self::$relationship);
    }

    protected static function getPluralModelLabel(): ?string
    {
        return trans(self::$relationship);
    }

    protected function getAttachHeading(): string
    {
        $label = AttachAction::make()->getLabel();
        return $label.' '.$this->getTableHeading();
    }
}
