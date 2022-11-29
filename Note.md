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
