<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributes';
    protected static ?string $title = 'Product Attributes';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('attribute_id')
                ->label('Attribute')
                ->options(Attribute::pluck('name', 'id'))
                ->required()
                ->live()
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('attribute_value_id', null)),

            Forms\Components\Select::make('attribute_value_id')
                ->label('Value')
                ->options(function ($get) {
                    $attributeId = $get('attribute_id');
                    if (!$attributeId) return [];

                    $attribute = Attribute::find($attributeId);
                    if (!$attribute) return [];

                    return $attribute->values()->pluck('value', 'id');
                })
                ->required(function ($get) {
                    $attributeId = $get('attribute_id');
                    if (!$attributeId) return false;

                    $attribute = Attribute::find($attributeId);
                    return $attribute && in_array($attribute->type, ['select', 'multiselect']);
                })
                ->live()
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('value')
                ->required(function ($get) {
                    $attributeId = $get('attribute_id');
                    if (!$attributeId) return false;

                    $attribute = Attribute::find($attributeId);
                    return $attribute && in_array($attribute->type, ['text', 'textarea', 'number']);
                })
                ->visible(function ($get) {
                    $attributeId = $get('attribute_id');
                    if (!$attributeId) return false;

                    $attribute = Attribute::find($attributeId);
                    return $attribute && in_array($attribute->type, ['text', 'textarea', 'number']);
                }),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attribute.name')
                    ->label('Attribute')
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(function ($record) {
                        if ($record->attributeValue) {
                            return $record->attributeValue->value;
                        }
                        return $record->value;
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
} 