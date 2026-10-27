<?php

namespace App\Filament\Resources\ActionMonitorings;

use App\Filament\Resources\ActionMonitorings\Pages\ManageActionMonitorings;
use BackedEnum;
use Binafy\LaravelUserMonitoring\Models\ActionMonitoring;
use Binafy\LaravelUserMonitoring\Utills\ActionType;
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

class ActionMonitoringResource extends Resource
{
    protected static ?string $model = ActionMonitoring::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Monitoring';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Aktivitas';

    protected static ?string $pluralModelLabel = 'Aktivitas';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function actionTypeColor(string $type): string
    {
        return match ($type) {
            ActionType::ACTION_STORE => 'success',
            ActionType::ACTION_UPDATE => 'info',
            ActionType::ACTION_DELETE => 'danger',
            ActionType::ACTION_RESTORED => 'warning',
            default => 'gray',
        };
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Pengguna')
                    ->placeholder('Tamu'),
                TextEntry::make('action_type')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => static::actionTypeColor($state)),
                TextEntry::make('table_name')
                    ->label('Tabel'),
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
                TextColumn::make('action_type')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => static::actionTypeColor($state)),
                TextColumn::make('table_name')
                    ->label('Tabel')
                    ->searchable(),
                TextColumn::make('ip')
                    ->label('IP')
                    ->searchable(),
                TextColumn::make('page')
                    ->label('Halaman')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action_type')
                    ->label('Aksi')
                    ->options(fn (): array => ActionMonitoring::query()
                        ->distinct()
                        ->orderBy('action_type')
                        ->pluck('action_type', 'action_type')
                        ->all()),
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
            'index' => ManageActionMonitorings::route('/'),
        ];
    }
}
