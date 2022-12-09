## サービスコンテナ

- コンテナの登録メソッド
  - bind()
  - singleton()
  - scoped()
- コンテナからの依存解決メソッド
  - make()
  - makeWith()
- メソッドの引数として自動的に依存注入することも可能  
  -> インターフェースで型指定する場合は、手動でコンテナに解決を指示する必要がある

```php
class Sample
{
    public $message;

    /**
     * コンストラクタの引数にクラスを指定することで
     * 自動的にインスタンス化することが可能
     * -> DI(Dependency injection: 依存注入)
     * -> 以下の場合 Message クラスをインスタンス化して渡してくれる
     * */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function run()
    {
        $this->message->send();
    }
}

class Message
{
    public function send()
    {
        echo ('メッセージ表示');
    }
}
```

## DI のメリット

- クラスの生成を集約できる
- クラスの構造を隠蔽できる  
  -> モックを使ったテストがしやすくなる

## サービスプロバイダ

- atrisan コマンドでサービスプロバイダの作成

```
$ php artisan make:provider SampleServiceProvider
```

- config/app.php の `providers` 配列にサービスプロバイダの登録
- コントローラ内で `make` 等を使用してサービスプロバイダの呼び出し

## 例外 + ログ

- PHP7 以降は Throwable で例外取得をする
- 以下のどちらかの方法で使用する
  - `use Throwable;` を記載する
  - `\Throwable` と指定する

## トランザクション

- `DB::transaction` を使用すると例外発生時のロールバック、処理完了時のコミットを自動で行うため、手動でコミットやロールバックを行う必要はない
- `DB::transaction` の第 2 引数に指定した数値の回数分、トランザクションの再試行が行われる

```php
DB::transaction(function () use ($request) {
    $owner = Owner::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);
    Shop::create([
        'owner_id' => $owner->id,
        'name' => '店名を入力してください',
        'information' => '',
        'filename' => '',
        'is_selling' => true,
    ]);
}, 2); // 2回再試行する
```

## ファイルアップロード

- `Storage::putFile` メソッドでファイル名を自動的に割り当てることが可能
- `$request->file('photo')->isValid()` で正常にアップロードされていることをバリデーションできる

```php
public function update(Request $request, $id)
{
    $imageFile = $request->image;
    if (!is_null($imageFile) && $imageFile->isValid()) {
        Storage::putFile('public/shops', $imageFile);
    }
    return redirect()->route('owner.shops.index');
}
```

## コントローラのレスポンス ※フロントも Laravel で実装する場合

基本的には以下をレスポンスとして返却する

- GET: view
- POST: redirect
