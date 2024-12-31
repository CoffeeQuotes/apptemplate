<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\Select::make('group')
                    ->options([
                        'general' => 'General',
                        'social' => 'Social Media',
                        'contact' => 'Contact',
                        'seo' => 'SEO',
                        'custom' => 'Custom',
                    ])
                    ->required()
                    ->columnSpan(1),

                Forms\Components\TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('This will be used as the key in the database and in the code.')
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('key', Str::slug($state, '_'));
                    })
                    ->columnSpan(1),

                Forms\Components\TextInput::make('display_name')
                    ->required()
                    ->columnSpan(1),

                    Forms\Components\Select::make('type')
                    ->options([
                        'text' => 'Text',
                        'textarea' => 'Text Area',
                        'number' => 'Number',
                        'boolean' => 'Boolean',
                        'select' => 'Select',
                        'image' => 'Image',
                        'json' => 'JSON',
                    ])
                    ->required()
                    ->live()
                    ->columnSpan(1),
                    Forms\Components\Textarea::make('options')
                    ->helperText('For select type, enter options one per line')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'select')
                    ->columnSpan(2),

                // Dynamic value field based on type
                Forms\Components\TextInput::make('value')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'text')
                    ->columnSpan(2),

                Forms\Components\Textarea::make('value')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'textarea')
                    ->columnSpan(2),

                Forms\Components\TextInput::make('value')
                    ->numeric()
                    ->visible(fn (Forms\Get $get) => $get('type') === 'number')
                    ->columnSpan(2),

                Forms\Components\Toggle::make('value')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'boolean')
                    ->columnSpan(2),

                Forms\Components\Select::make('value')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'select')
                    ->options(fn (Forms\Get $get) => collect(explode("\n", $get('options') ?? ''))->mapWithKeys(fn ($item) => [$item => $item]))
                    ->columnSpan(2),

                Forms\Components\FileUpload::make('value')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                    ->image()
                    ->directory('settings')
                    ->columnSpan(2),

                Forms\Components\Textarea::make('value')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                    ->columnSpan(2),

                Forms\Components\Toggle::make('is_public')
                    ->default(true)
                    ->columnSpan(1),

                Forms\Components\Textarea::make('description')
                    ->columnSpan(2),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_public')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'general' => 'General',
                        'social' => 'Social Media',
                        'contact' => 'Contact Information',
                        'seo' => 'SEO',
                        'custom' => 'Custom',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'text' => 'Text',
                        'textarea' => 'Text Area',
                        'number' => 'Number',
                        'boolean' => 'Boolean',
                        'select' => 'Select',
                        'image' => 'Image',
                        'json' => 'JSON',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
