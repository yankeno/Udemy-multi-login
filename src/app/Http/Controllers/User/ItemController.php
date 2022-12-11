<?php

namespace App\Http\Controllers\User;

use App\Models\Stock;
use App\Models\Product;
use Illuminate\View\View;
use App\Services\ItemService;
use App\Http\Controllers\Controller;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');
    }

    public function index(ItemService $itemService): View
    {
        $stocks = $itemService->loadItemStocksBuilder();
        $products = $itemService->fetchPurchasableProducts($stocks);
        return view('user.index', compact('products'));
    }

    public function show($id): View
    {
        $product = Product::findOrFail($id);

        $quantity =  Stock::where('product_id', $product->id)
            ->sum('quantity');

        if ($quantity > 9) {
            $quantity = 9;
        }

        return view('user.show', compact('product', 'quantity'));
    }
}
