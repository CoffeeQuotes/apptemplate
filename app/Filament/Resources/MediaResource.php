<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Filament\Resources\MediaResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;



class MediaResource extends Resource
{
    protected static ?string $model = Media::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Hidden::make('model_type')
                            ->default('App\Models\Media'),
                        
                        Forms\Components\Hidden::make('model_id')
                            ->default(1),

                        Forms\Components\Hidden::make('collection_name'),

                        Forms\Components\Hidden::make('disk')
                            ->default('public'),

                        Forms\Components\Hidden::make('size')
                            ->default(0),

                        Forms\Components\Hidden::make('manipulations')
                            ->default([]),

                        Forms\Components\Hidden::make('generated_conversions')
                            ->default([]),

                        Forms\Components\Hidden::make('responsive_images')
                            ->default([]),

                        Forms\Components\Select::make('type')
                            ->options([
                                'image' => 'Image',
                                'video' => 'Video',
                                'document' => 'Document',
                            ])
                            ->required()
                            ->live()
                            ->disabled(fn ($record) => $record !== null)
                            ->dehydrated(fn ($record) => $record === null)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $type = $get('type');
                                $set('collection_name', $type . 's');
                            }),

                        FileUpload::make('file_name')
                            ->disk('public')
                            ->directory('media')
                            ->visibility('public')
                            ->acceptedFileTypes(function (Get $get) {
                                $type = $get('type');
                                return match ($type) {
                                    'document' => [
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.oasis.opendocument.text',
                                        'text/plain',
                                    ],
                                    'image' => [
                                        'image/jpeg',
                                        'image/png',
                                        'image/gif',
                                        'image/webp',
                                    ],
                                    'video' => [
                                        'video/mp4',
                                        'video/quicktime',
                                        'video/x-msvideo',
                                        'video/mpeg',
                                        'video/webm',
                                    ],
                                    default => [],
                                };
                            })
                            ->imageEditor(fn (Get $get) => $get('type') === 'image')
                            ->imageEditorMode(2)
                            ->imageEditorViewportWidth('1920')
                            ->imageEditorViewportHeight('1080')
                            ->imagePreviewHeight(fn (Get $get) => $get('type') === 'image' ? '100' : null)
                            ->downloadable()
                            ->openable()
                            ->previewable(fn (Get $get) => $get('type') === 'image')
                            ->preserveFilenames()
                            ->maxSize(102400)
                            ->columnSpanFull()
                            ->required()
                            ->helperText(fn (Get $get) => match($get('type')) {
                                'image' => 'Accepted formats: JPG, PNG, GIF, WebP (max 100MB)',
                                'document' => 'Accepted formats: PDF, DOC, DOCX, ODT, TXT (max 100MB)',
                                'video' => 'Accepted formats: MP4, MOV, AVI, MPEG, WebM (max 100MB)',
                                default => 'Please select a type first',
                            })
                            ->rules(function (Get $get) {
                                $type = $get('type');
                                return match ($type) {
                                    'document' => ['file', 'mimes:pdf,doc,docx,odt,txt', 'max:102400'],
                                    'image' => ['image', 'mimes:jpeg,png,gif,webp', 'max:102400'],
                                    'video' => ['file', 'mimes:mp4,mov,avi', 'max:102400'],
                                    default => ['required'],
                                };
                            }),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('alt_text')
                            ->maxLength(255)
                            ->helperText('Alternative text for accessibility and SEO')
                            ->afterStateUpdated(function ($state, $record) {
                                if ($record && !empty($state)) {
                                    $customProperties = $record->custom_properties;
                                    unset($customProperties['alt']);
                                    $record->custom_properties = $customProperties;
                                    $record->save();
                                }
                            }),

                        Forms\Components\Select::make('folder')
                            ->options([
                                'general' => 'General',
                                'products' => 'Products',
                                'blog' => 'Blog',
                                'gallery' => 'Gallery',
                            ])
                            ->searchable(),
                        
                        Forms\Components\Textarea::make('caption')
                            ->rows(2)
                            ->maxLength(500),
                        
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000),

                        Forms\Components\KeyValue::make('custom_properties')
                            ->helperText('Add custom metadata as key-value pairs')
                            ->deleteButtonLabel('Remove')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('url')
                    ->label('Preview')
                    ->square()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->visible(fn ($record) => $record && $record->type === 'image'),

                Tables\Columns\TextColumn::make('file_name')
                    ->label('File')
                    ->visible(fn ($record) => $record && $record->type !== 'image')
                    ->url(fn ($record) => $record ? $record->url : null)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'success',
                        'document' => 'info',
                        'video' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('folder')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'image' => 'Image',
                        'document' => 'Document',
                        'video' => 'Video',
                    ]),
                Tables\Filters\SelectFilter::make('folder')
                    ->options([
                        'general' => 'General',
                        'products' => 'Products',
                        'blog' => 'Blog',
                        'gallery' => 'Gallery',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }
}
