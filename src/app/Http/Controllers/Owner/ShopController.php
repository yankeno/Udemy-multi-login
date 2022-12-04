<?php

namespace App\Http\Controllers\Owner;

use App\Models\Shop;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ImageService;
use App\Http\Requests\UploadImageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        $this->middleware(function ($request, $next) {
            $id = $request->route()->parameter('shop'); //shopのid取得
            if (!is_null($id)) { // null判定
                $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                $shopId = (int)$shopsOwnerId; // キャスト 文字列→数値に型変換
                $ownerId = Auth::id();
                if ($shopId !== $ownerId) { // 同じでなかったら
                    abort(404); // 404画面表示
                }
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        $shops = Shop::where('owner_id', Auth::id())->get();
        return view('owner.shops.index', compact('shops'));
    }

    public function edit($id)
    {
        $shop = Shop::findOrFail($id);
        return view('owner.shops.edit', compact('shop'));
    }

    public function update(UploadImageRequest $request, int $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'information' => 'required|string|max:1000',
            'is_selling' => 'required',
        ]);

        $shop = Shop::findOrFail($id);

        $imageFile = $request->image;
        if (!is_null($imageFile) && $imageFile->isValid()) {
            $fileNameToStore = ImageService::upload($imageFile, 'shops');
            $shop->filename = $fileNameToStore;
        }

        $shop->name = $request->name;
        $shop->information = $request->information;
        $shop->is_selling = $request->is_selling;
        $shop->save();

        return redirect()->route('owner.shops.index')->with([
            'message' => '店舗情報を登録しました。',
            'status' => 'info',

        ]);
    }
}
