<x-tests.app>
    <x-slot name="header">
        ヘッダー1
    </x-slot>
    コンポーネントテスト1

    {{-- :属性名='$変数名' の形式で記述する --}}
    <x-tests.card title="タイトル1" content="本文" :message="$message" />

    {{-- 初期値が表示される --}}
    <x-tests.card title="タイトル2" />

    {{-- class を card に渡す --}}
    <x-tests.card title="CSSを変更したい" class="bg-red-300" />
</x-tests.app>
