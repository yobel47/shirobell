<?php

namespace App\Filament\Resources\MusicTracks;

use App\Filament\Resources\MusicTracks\Pages\CreateMusicTrack;
use App\Filament\Resources\MusicTracks\Pages\EditMusicTrack;
use App\Filament\Resources\MusicTracks\Pages\ListMusicTracks;
use App\Models\MusicTrack;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class MusicTrackResource extends Resource
{
    protected static ?string $model = MusicTrack::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMusicalNote;

    protected static string|UnitEnum|null $navigationGroup = 'Katalog';

    protected static ?string $modelLabel = 'lagu';

    protected static ?string $pluralModelLabel = 'Perpustakaan musik';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('title')->label('Judul')->required()->maxLength(150),
                TextInput::make('artist')->label('Artis')->maxLength(150),
                FileUpload::make('file_path')->label('File MP3')->required()->disk('public')->directory('music')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3'])->maxSize(10240)->columnSpanFull()
                    ->helperText('Pastikan lagu bebas royalti atau berlisensi untuk dipakai di undangan.'),
                Toggle::make('is_active')->label('Aktif')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('title')
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->description(fn (MusicTrack $r) => $r->artist),
                TextColumn::make('invitations_count')->label('Dipakai')->counts('invitations')->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->after(fn (MusicTrack $record) => Storage::disk('public')->delete($record->file_path)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMusicTracks::route('/'),
            'create' => CreateMusicTrack::route('/create'),
            'edit' => EditMusicTrack::route('/{record}/edit'),
        ];
    }
}
