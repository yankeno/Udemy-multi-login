<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Product;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');

        $this->middleware(function ($request, $next) {
            $id = $request->route()->parameter('item');
            $isShowable = Product::availableItems()->where('product_id', $id)->exists();
            if (!is_null($id)) {
                if (!$isShowable) {
                    abort(404);
                }
            }
            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $products = Product::availableItems()
            ->sortOrder($request->sort)
            ->paginate((int) $request->pagination ?? 20);
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