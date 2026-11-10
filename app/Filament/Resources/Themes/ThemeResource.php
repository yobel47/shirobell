<?php

namespace App\Filament\Resources\Themes;

use App\Filament\Resources\Themes\Pages\EditTheme;
use App\Filament\Resources\Themes\Pages\ListThemes;
use App\Models\Theme;
use BackedEnum;
use Closure;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Themes are code (Blade views); admin manages only their catalogue data. */
class ThemeResource extends Resource
{
    protected static ?string $model = Theme::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string|UnitEnum|null $navigationGroup = 'Katalog';

    protected static ?string $modelLabel = 'tema';

    protected static ?string $pluralModelLabel = 'Tema';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('slug')->label('Slug (folder view)')->disabled(),
                TextInput::make('name')->label('Nama')->required()->maxLength(100),
                TextInput::make('description')->label('Deskripsi')->maxLength(255)->columnSpanFull(),
                Select::make('badge')->options(['new' => 'Baru', 'popular' => 'Populer'])->placeholder('Tanpa badge'),
                TextInput::make('sort_order')->label('Urutan')->integer()->default(0),
                FileUpload::make('thumbnail_path')->label('Thumbnail')->image()->disk('public')->directory('themes')
                    ->maxSize(4096)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                Toggle::make('is_active')->label('Aktif (bisa dipilih customer)')->inline(false)
                    ->rules([fn (?Theme $record) => function (string $attribute, $value, Closure $fail) use ($record) {
                        if ($value && $record && ! $record->hasView()) {
                            $fail('Tema belum punya tampilan (resources/views/themes/'.$record->slug.'/index.blade.php).');
                        }
                    }]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('thumbnail_path')->label('')->disk('public')->square(),
                TextColumn::make('name')->label('Nama')->description(fn (Theme $r) => $r->slug),
                TextColumn::make('invitations_count')->label('Dipakai')->counts('invitations')->sortable(),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
                IconColumn::make('view')->label('Tampilan siap')->boolean()->state(fn (Theme $r) => $r->hasView()),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThemes::route('/'),
            'edit' => EditTheme::route('/{record}/edit'),
        ];
    }
}
