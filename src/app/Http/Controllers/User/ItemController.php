<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ItemService;

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
        return view('user.show', compact('product'));
    }
}
