<?php

namespace App\Filament\Resources\Brands;

use App\Filament\Resources\Brands\Pages;
use App\Filament\Resources\Brands\Schemas\BrandForm;
use App\Filament\Resources\Brands\Schemas\BrandInfolist;
use App\Filament\Resources\Brands\Tables\BrandsTable;
use App\Models\Brand;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | \BackedEnum | null $navigationIcon =
        Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return BrandForm::configure($schema);
    }
    public static function infolist(Schema $schema): Schema
    {
        return BrandInfolist::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return BrandsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'view' => Pages\ViewBrand::route('/{record}'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
    public static function getNavigationLabel(): string
    {
        return __('saqr.brands.plural');
    }

    public static function getModelLabel(): string
    {
        return __('saqr.brands.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('saqr.brands.plural');
    }

}
