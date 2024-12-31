<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $state, Forms\Set $set) => 
                                    $set('slug', Str::slug($state))),

                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true),

                            Forms\Components\TextInput::make('sku')
                                ->label('SKU')
                                ->required()
                                ->unique(ignoreRecord: true),

                            Forms\Components\MarkdownEditor::make('description')
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('short_description')
                                ->columnSpanFull(),
                        ])->columns(2),

                    Forms\Components\Section::make('Pricing & Inventory')
                        ->schema([
                            Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->required()
                                ->prefix('$'),

                            Forms\Components\TextInput::make('sale_price')
                                ->numeric()
                                ->prefix('$')
                                ->lte('price'),

                            Forms\Components\TextInput::make('stock')
                                ->numeric()
                                ->default(0)
                                ->required(),
                        ])->columns(3),

                    Forms\Components\Section::make('Images')
                        ->schema([
                            Forms\Components\Grid::make()
                                ->schema([
                            FileUpload::make('images')
                                ->disk('public')
                                ->directory('products')
                                ->multiple()
                                ->maxFiles(5)
                                ->reorderable()
                                ->image()
                                ->imageEditor()
                                ->columnSpanFull()
                                ->imagePreviewHeight('100')
                                ->loadingIndicatorPosition('left')
                                ->panelLayout('grid')
                                ->panelAspectRatio('16:9')
                                ->maxSize(5120)
                                ->removeUploadedFileButtonPosition('right')
                                ->uploadButtonPosition('left')
                                ->uploadProgressIndicatorPosition('left')
                                ->extraAttributes(['class' => 'max-h-[300px] overflow-y-auto'])
                                ->getUploadedFileNameForStorageUsing(
                                    fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                        ->prepend(now()->timestamp . '_'),
                                )
                                ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                    if (!$record || empty($state)) return;
                                    
                                    // Delete old images first
                                    $record->images()->delete();
                                    
                                    // Create new images
                                    collect($state)->each(function ($path, $index) use ($record) {
                                        $record->images()->create([
                                            'path' => $path,
                                            'sort_order' => $index,
                                            'is_primary' => $index === 0,
                                        ]);
                                    });
                                })
                                ->default(function ($record) {
                                    if (!$record) return [];
                                    
                                    return $record->images()
                                        ->orderBy('sort_order')
                                        ->get()
                                                ->map(function ($image) {
                                                    return str_replace('products/', '', $image->path);
                                                })
                                        ->toArray();
                                        })
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                        ->helperText('Upload up to 5 product images. First image will be set as primary.')
                                        ->downloadable()
                                        ->openable()
                                        ->previewable()
                                        ->imageResizeMode('cover')
                                        ->imageCropAspectRatio('16:9')
                                        ->imageResizeTargetWidth('1920')
                                        ->imageResizeTargetHeight('1080')
                                        ->visibility('public'),
                                ])
                        ])
                        ->collapsible(),

            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make('Status')
                        ->schema([
                            Forms\Components\Toggle::make('is_visible')
                                ->label('Visible')
                                ->helperText('Make this product visible to customers')
                                ->default(true),

                            Forms\Components\Toggle::make('is_featured')
                                ->label('Featured')
                                ->helperText('Feature this product on the homepage'),

                            Forms\Components\Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                ])
                                ->default('draft')
                                ->required(),

                            Forms\Components\DateTimePicker::make('published_at')
                                ->label('Publish Date'),
                        ]),

                    Forms\Components\Section::make('Associations')
                        ->schema([
                            Forms\Components\Select::make('categories')
                                ->relationship('categories', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable(),
                        ]),

                    Forms\Components\Section::make('SEO')
                        ->schema([
                            Forms\Components\TextInput::make('seo_title')
                                ->label('SEO Title')
                                ->maxLength(60),
                            
                            Forms\Components\Textarea::make('seo_description')
                                ->label('SEO Description')
                                ->maxLength(160),
                            
                            Forms\Components\TextInput::make('seo_keywords')
                                ->label('SEO Keywords')
                                ->maxLength(255),
                        ]),
                ])->columnSpan(['lg' => 1]),
                ])->columnSpan(['lg' => 3])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primary_image')
                    ->label('Image')
                    ->disk('public')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean()
                    ->label('Visible')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Visibility')
                    ->boolean()
                    ->trueLabel('Visible only')
                    ->falseLabel('Hidden only')
                    ->native(false),
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
            RelationManagers\AttributesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
} 