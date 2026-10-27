<?php

namespace App\Filament\Resources\VisitMonitorings;

use App\Filament\Resources\VisitMonitorings\Pages\ManageVisitMonitorings;
use BackedEnum;
use Binafy\LaravelUserMonitoring\Models\VisitMonitoring;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class VisitMonitoringResource extends Resource
{
    protected static ?string $model = VisitMonitoring::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEye;

    protected static string|UnitEnum|null $navigationGroup = 'Monitoring';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Kunjungan';

    protected static ?string $pluralModelLabel = 'Kunjungan';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Pengguna')
                    ->placeholder('Tamu'),
                TextEntry::make('user_guard')
                    ->label('Guard')
                    ->placeholder('-'),
                TextEntry::make('browser_name')
                    ->label('Browser'),
                TextEntry::make('platform'),
                TextEntry::make('device')
                    ->label('Perangkat'),
                TextEntry::make('ip')
                    ->label('IP'),
                TextEntry::make('page')
                    ->label('Halaman')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Waktu')
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->placeholder('Tamu')
                    ->searchable(),
                TextColumn::make('page')
                    ->label('Halaman')
                    ->limit(60)
                    ->tooltip(fn (VisitMonitoring $record): string => $record->page)
                    ->searchable(),
                TextColumn::make('ip')
                    ->label('IP')
                    ->searchable(),
                TextColumn::make('browser_name')
                    ->label('Browser')
                    ->toggleable(),
                TextColumn::make('platform')
                    ->toggleable(),
                TextColumn::make('device')
                    ->label('Perangkat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageVisitMonitorings::route('/'),
        ];
    }
}
