<?php

namespace App\Filament\Resources\AuthenticationMonitorings;

use App\Filament\Resources\AuthenticationMonitorings\Pages\ManageAuthenticationMonitorings;
use BackedEnum;
use Binafy\LaravelUserMonitoring\Models\AuthenticationMonitoring;
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

class AuthenticationMonitoringResource extends Resource
{
    protected static ?string $model = AuthenticationMonitoring::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static string|UnitEnum|null $navigationGroup = 'Monitoring';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Autentikasi';

    protected static ?string $pluralModelLabel = 'Autentikasi';

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
                    ->placeholder('-'),
                TextEntry::make('action_type')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'login' ? 'success' : 'gray'),
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
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('action_type')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'login' ? 'success' : 'gray'),
                TextColumn::make('ip')
                    ->label('IP')
                    ->searchable(),
                TextColumn::make('browser_name')
                    ->label('Browser')
                    ->toggleable(),
                TextColumn::make('platform')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('action_type')
                    ->label('Aksi')
                    ->options([
                        'login' => 'Login',
                        'logout' => 'Logout',
                    ]),
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
            'index' => ManageAuthenticationMonitorings::route('/'),
        ];
    }
}
