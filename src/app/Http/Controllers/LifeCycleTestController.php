<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LifeCycleTestController extends Controller
{
    public function showServiceProviderTest()
    {
        $encrypt = app()->make('encrypter');
        $password = $encrypt->encrypt('password');

        $sample = app()->make('serviceProviderTest');

        dd($sample, $password, $encrypt->decrypt($password));
    }

    public function showServiceContainerTest()
    {
        app()->bind('lifeCycleTest', function () {
            return 'ライフサイクルテスト';
        });
        $test = app()->make('lifeCycleTest');

        // サービスコンテナなしのパターン
        // $message = new Message();
        // $sample = new Sample($message);
        // $sample->run();

        // サービスコンテナ app() ありのパターン
        app()->bind('sample', Sample::class); // sample という名前でクラスをバインド
        $sample = app()->make('sample'); // make で sample を指定して値を取り出す -> 今回は Sample クラス
        $sample->run();

        dd($test, app());
    }
}

class Sample
{
    public $message;

    /**
     * コンストラクタの引数にクラスを指定することで
     * 自動的にインスタンス化することが可能
     * -> DI(Dependency injection: 依存注入)
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
