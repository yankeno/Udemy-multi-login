<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ItemService
{
    public function loadItemStocksBuilder(): Builder
    {
        return DB::table('t_stocks')
            ->select('product_id', DB::raw('SUM(quantity) AS quantity'))
            ->groupBy('product_id')
            ->having('quantity', '>', 1);
    }

    public function fetchPurchasableProducts(Builder $stocks): Collection
    {
        return DB::table('products')
            ->joinSub($stocks, 'stock', function ($join) {
                $join->on('products.id', '=', 'stock.product_id');
            })
            ->join('shops', 'products.shop_id', '=', 'shops.id')
            ->join(
                'secondary_categories',
                'products.secondary_category_id',
                '=',
                'secondary_categories.id'
            )
            ->join('images as image1', 'products.image1', '=', 'image1.id')
            ->join('images as image2', 'products.image2', '=', 'image2.id')
            ->join('images as image3', 'products.image3', '=', 'image3.id')
            ->join('images as image4', 'products.image4', '=', 'image4.id')
            ->where('shops.is_selling', true)
            ->where('products.is_selling', true)
            ->select(
                'products.id as id',
                'products.name as name',
                'products.price',
                'products.sort_order as sort_order',
                'products.information',
                'secondary_categories.name as category',
                'image1.filename as filename'
            )
            ->get();
    }
}
