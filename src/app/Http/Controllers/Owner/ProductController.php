<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        $this->middleware(function ($request, $next) {
            $id = $request->route()->parameter('product');
            if (!is_null($id)) {
                $productOwnerId = Product::findOrFail($id)->shop->owner->id;
                $productId = (int) $productOwnerId;
                if ($productId !== Auth::id()) {
                    abort(404);
                }
            }
            return $next($request);
        });
    }
    public function index()
    {
        $ownerInfo = Owner::with('shop.product.imageFirst')
            ->where('id', Auth::id())
            ->get();
        // dd($ownerInfo);
        // foreach ($ownerInfo as $owner) {
        //     foreach ($owner->shop->product as $product) {
        //         dd($product->imageFirst->filename);
        //     }
        // }
        return view('owner.products.index', compact('ownerInfo'));
    }
}
