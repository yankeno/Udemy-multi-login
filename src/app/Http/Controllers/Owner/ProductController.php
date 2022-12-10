<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Image;
use App\Models\Owner;
use App\Models\PrimaryCategory;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
    public function index(): View
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

    public function create(): View
    {
        $shops = Shop::where('owner_id', Auth::id())
            ->select('id', 'name')
            ->get();

        $images = Image::where('owner_id', Auth::id())
            ->select('id', 'title', 'filename')
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = PrimaryCategory::with('secondary')->get();

        return view('owner.products.create', compact('shops', 'images', 'categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $product = Product::create([
                    'shop_id' => $request->shop_id,
                    'name' => $request->name,
                    'information' => $request->information,
                    'price' => $request->price,
                    'is_selling' => $request->is_selling,
                    'sort_order' => $request->sort_order,
                    'secondary_category_id' => $request->category,
                    'image1' => $request->image1,
                    'image2' => $request->image2,
                    'image3' => $request->image3,
                    'image4' => $request->image4,
                ]);
                Stock::create([
                    'product_id' => $product->id,
                    'type' => 1,
                    'quantity' => $request->quantity,
                ]);
            }, 2);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }

        return redirect()->route('owner.products.index')
            ->with([
                'message' => '商品登録を実施しました。',
                'status' => 'info'
            ]);
    }

    public function edit(int $id): View
    {
        $product = Product::findOrFail($id);

        $quantity =  Stock::where('product_id', $product->id)
            ->sum('quantity');

        $shops = Shop::where('owner_id', Auth::id())
            ->select('id', 'name')
            ->get();

        $images = Image::where('owner_id', Auth::id())
            ->select('id', 'title', 'filename')
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = PrimaryCategory::with('secondary')->get();

        return view(
            'owner.products.edit',
            compact('product', 'quantity', 'shops', 'images', 'categories')
        );
    }

    public function update(ProductRequest $request, int $id): RedirectResponse
    {
        $request->validate([
            'current_quantity' => 'required|integer',
        ]);
        $product = Product::findOrFail($id);
        $quantity =  Stock::where('product_id', $product->id)
            ->sum('quantity');

        if ($request->current_quantity !== $quantity) {
            $id = $request->route()->parameter('product');
            return redirect()
                ->route('owner.products.edit', ['product' => $id])
                ->with([
                    'message' => '在庫数が変更されています。再度確認してください。',
                    'status' => 'alert',
                ]);
        } else {
            try {
                DB::transaction(function () use ($request, $product) {
                    $product->shop_id = $request->shop_id;
                    $product->name = $request->name;
                    $product->information = $request->information;
                    $product->price = $request->price;
                    $product->is_selling = $request->is_selling;
                    $product->sort_order = $request->sort_order;
                    $product->secondary_category_id = $request->category;
                    $product->image1 = $request->image1;
                    $product->image2 = $request->image2;
                    $product->image3 = $request->image3;
                    $product->image4 = $request->image4;
                    $product->save();
                    if ($request->type === \Constant::PRODUCT_LIST['add']) {
                        $newQuantity = $request->quantity;
                    }
                    if ($request->type === \Constant::PRODUCT_LIST['reduce']) {
                        $newQuantity = $request->quantity * -1;
                    }
                    Stock::create([
                        'product_id' => $product->id,
                        'type' => 1,
                        'quantity' => $newQuantity,
                    ]);
                }, 2);
            } catch (Throwable $e) {
                Log::error($e);
                throw $e;
            }

            return redirect()->route('owner.products.index')
                ->with([
                    'message' => '商品情報を更新しました。',
                    'status' => 'info'
                ]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('owner.products.index')
            ->with([
                'message' =>  '商品を削除しました。',
                'status' => 'alert',
            ]);
    }
}
