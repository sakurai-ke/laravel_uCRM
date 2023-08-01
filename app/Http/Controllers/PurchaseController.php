<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Purchase;
use Inertia\Inertia;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(Order::paginate(50));

        // 合計
        // groupBy('id') メソッドを使用して結果を id でグループ化
        // （同じidがある場合、そのid（purchases.id）を一つのグループとしてtotal値を出している。要するに一回の購買での合計値
        $orders = Order::groupBy('id')
        // id、customer_name、subtotal の合計値を total として表示し、status、created_at も取得
        // また、各商品の商品をそれぞれ合計した値をsum(subtotal) as totalとして定義している
        // （Subtotal.phpよりpurchases.idがidとして宣言されており、そのidをグループとして
        // 購入金額の合計をtotalとして宣言している。）
        // あと、selectRaw メソッドを使うことで、SQL の集計関数（例：SUM、COUNT、AVG など）を利用してデータを集計することができる。
        // 通常の select メソッドでは、集計関数を利用することができません。
        ->selectRaw('id, customer_name,
        sum(subtotal) as total, status, created_at' )
        ->paginate(50);

        return Inertia::render('Purchases/Index', [
        'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $customers = Customer::select('id', 'name', 'kana')->get();
        // ->where('is_selling',true)にて販売中のものだけを取得することにしている
        $items = Item::select('id', 'name', 'price')->where('is_selling',
        true)->get();
        return Inertia::render('Purchases/Create', [
        // 'customers' => $customers,
        'items' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseRequest $request)
    {
         // dd($request);

        //  DB::beginTransaction();について、このメソッドは、トランザクションを開始するために呼び出されます。トランザクションが
        // 開始されると、のトランザクション内で行われる全てのデータベース操作は、一連の処理としてまとめられます。
        // このメソッドはトランザクションを開始するだけで、実際のデータベース操作はこの後に記述する必要があります。
        // (今回はpurchaseテーブルと中間テーブル同時にデータベースに追加するので両方のテーブルとも保存が成功した場合に
        // 保存動作がされ、どちらかのテーブルへの保存が失敗した場合はロールバックして戻せるようにする。)
        DB::beginTransaction();

        // tryブロック内には、例外が発生する可能性のあるコードを記述します。このブロック内で例外が発生すると、
        // それに対応するcatchブロックが実行されます。
        try{
            // $fillable プロパティにより、create メソッドによるデータベースへの保存が許可されるカラムが指定される
            $purchase = Purchase::create([
                // $request->customer_id と $request->status の値がPurchase レコードに挿入される
                'customer_id' => $request->customer_id,
                'status' => $request->status
            ]);
    
            // Create.vueファイルから渡ってきたitemsssを１つづつ処理
            foreach($request->itemsss as $item){
                // attachメソッドは中間テーブルに新しいレコードを追加するために使用されます。
                // 自然な記述として$purchase->items() のように、購入を基点に商品情報を取得できるような形式が通常、らしい
                $purchase->items()->attach($purchase->id, [
                    // 「$purchase->id」は中間テーブルの主キーとなり、第一引数としており、
                    // 商品データのID（$item['id']）と商品の数量（$item['quantity']）を「item_id」と「quantity」に追加する
                    // （'id'と'quantity'はCreate.vueファイルのstorePurchaseメソッドでから渡ってきた値）
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }
    
            // このメソッドは、トランザクション内の全てのデータベース操作が成功した場合にトランザクションをコミット（確定）するために使用されます。
            DB::commit();

            return to_route('dashboard');
    
            // catchブロックは例外をキャッチする際に、パラメータとして$e（または他の名前）という変数を使用します。この変数には、
            // tryブロック内で発生した例外オブジェクトが格納されます。例外オブジェクトには、例外の種類やメッセージ、ファイル名、
            // 行番号などの情報が含まれており、これを利用して例外に対応する処理を行うことができます。
        } catch(\Exception $e){
            // このメソッドは、トランザクション内のいずれかのデータベース操作が失敗した場合に
            // トランザクションをロールバック（取り消し）するために使用されます。
            DB::rollback();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
         // 小計
        //  一回の購買について、それぞれの商品について何個購入したか、各商品の小計などの情報$itemsに入る。
        // （商品Aは***個購入して小計は***円、商品Bは***個購入して小計は***円、商品Cは***個購入して小計は***円、などの情報）
         $items = Order::where('id', $purchase->id)->get();

         // 合計
          // （一回の購買について商品A、B、C全て数量、合計値などの情報を$orderに入れる）
        //   「 ->where('id', $purchase->id)」にて特定の$purchaseのidのデータを取得することを指定している。
        //   各商品の商品をそれぞれ合計した値をsum(subtotal) as totalとして定義している。
         $order = Order::groupBy('id')
         ->where('id', $purchase->id)
         ->selectRaw('id, sum(subtotal) as total, 
         customer_name, status, created_at')
         ->get();
 
         // dd($items, $order);
 
         return Inertia::render('Purchases/Show', [
             'items' => $items,
             'order' => $order
         ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        // purchaseテーブルから指定された購買idを取得して$purchaseに代入
        $purchase = Purchase::find($purchase->id);
        // itemテーブルから'id', 'name', 'price'を取得して$allItemsに代入（$allItemsは全ての商品の情報が格納された配列）
        $allItems = Item::select('id', 'name', 'price')
        ->get();
        // 上記２つから新しい配列を作成する???
        $items = [];

        // foreachループが$allItemsの各商品（$allItem）に対して順番に実行されます。
        foreach($allItems as $allItem){
            // 一旦数量の初期値を0とし、中間テーブルに情報があれば更新する
            $quantity = 0;
            // 「$purchase->items」は、特定の購入（Purchase）に関連付けられている全ての商品（Item）を取得するためのEloquentのリレーション（関連）
            // 具体的には、1つの購入（Purchase）に複数の商品（Item）が関連付けられている場合、その購入に関連する全ての商品を取得していることを意味している
            // 要するに一回の購入で３種類のアイテムを購入している場合、３回ループを回し、各アイテム情報を代入する
            foreach($purchase->items as $item){
                // if($allItem->id === $item->id)は、現在のループの商品（$allItem）と特定の購入に関連付けられた商品（$item）のIDが一致するかを比較します。
                // もし一致する場合、つまり特定の購入に含まれる商品が全ての商品の中で見つかった場合、その商品の数量を取得し、$quantityとして変数に代入します。
                if($allItem->id === $item->id){
                // $item->quantity という記述では、$item モデル自体が Item テーブルのレコードを表すものであり、そのモデルには 
                // quantity というカラムが存在しないため、直接この記述では中間テーブルの quantity カラムにアクセスすることはできません。
                // $item->pivot->quantity という記述は、$item モデルと Purchase モデルの間の中間テーブル Item_Purchase のレコードを表すオブジェクト（pivot）
                // を取得し、そのオブジェクトから quantity カラムの値を取得していることを意味します。
                    $quantity = $item->pivot->quantity;
                }
            }
            // 'id'：$allItem->id の値を代入しています。これは商品のIDを表します。
            // 'name'：$allItem->name の値を代入しています。これは商品の名前を表します。
            // 'price'：$allItem->price の値を代入しています。これは商品の単価を表します。
            // 'quantity'：変数 $quantity の値を代入しています。これは特定の商品が購入に含まれる数量を表します。（商品の数量が0であれば０のままとなる）
            array_push($items, [
                'id' => $allItem->id,
                'name' => $allItem->name,
                'price' => $allItem->price,
                'quantity' => $quantity,
            ]);
        }
       
        // dd($items);
        $order = Order::groupBy('id')
        ->where('id', $purchase->id)
        ->selectRaw('id, customer_id, 
        customer_name, status, created_at')
        ->get();

        return Inertia::render('Purchases/Edit', [
            'items' => $items,
            'order' => $order
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePurchaseRequest  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        DB::beginTransaction();

        try{
        // dd($request, $purchase);
        // purchaseのstatusのみ更新される可能性があるのでstatsのみ更新する
        $purchase->status = $request->status;
        $purchase->save();

        $items = [];

        // // Edit.vueファイルから渡ってきたitemsssを１つづつ処理
        foreach($request->itemsss as $item){
        // $item の中の id をキーとして、quantity の値を持つ連想配列を作成し、それを $items という配列に追加しています。
        // 要するに、$items 配列は、商品IDをキーとし、その商品の数量を値とする連想配列を要素として持つ形になります。
        // ループが終了するまで、上記の処理を繰り返すことで、$items 配列には全ての商品の数量情報が格納されます。
            $items = $items + [
                $item['id'] => [
                    'quantity' => $item['quantity']
                ]
            ];
        }

        // dd($items);

        // $purchase->items() は、特定の購入（Purchase）と関連付けられている商品（Item）を取得するEloquentリレーション（関連）です。
        // このメソッドを呼び出すことで、購入に関連付けられている商品の中間テーブルを取得します。sync($items) メソッドを呼び出すことで、
        // 中間テーブルのデータを $items 配列の内容に同期します。つまり、$items 配列に含まれる商品と数量情報に基づいて、中間テーブルの
        // レコードを更新します。sync() メソッドは、次の3つのアクションを行います：
        // 中間テーブルに含まれるが、$items 配列には含まれない商品のレコードを削除します。
        // $items 配列に含まれるが、中間テーブルには含まれない商品のレコードを追加します。
        // $items 配列に含まれる商品が中間テーブルに既に存在している場合は、数量情報を更新します。
        // つまり、$items 配列に含まれる商品と数量情報に基づいて、中間テーブルのレコードを更新することで、購入と商品の関連付けが最新の情報になります。
        $purchase->items()->sync($items);

        DB::commit();
        return to_route('dashboard');
    } catch(\Exception $e){
        DB::rollback();
    }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}



// $items = $items + [
//     $item['id'] => [
//         'quantity' => $item['quantity']
//     ]
// ];
// のコードについては下記のように考えます。
// $items = [] では、空の連想配列 $items を初期化しています。この配列には、後で各商品の数量情報を格納することになります。

// 次に、foreach ループを使用して $_POST['items'] 配列を処理しています。$_POST['items'] 配列には、ユーザーがフォームで入力した商品の数量情報が含まれています。

// ループ内の処理は、次のようになります：

// ループは $_POST['items'] 配列の各要素を順番に取り出します。各要素は $item という変数に格納されます。
// $item['id'] は、ループで取り出した要素（商品情報）の中の "id" キーの値を表します。これは商品の一意の識別子、例えば商品IDです。
// $item['quantity'] は、ループで取り出した要素（商品情報）の中の "quantity" キーの値を表します。これはユーザーが入力した数量、つまり商品の個数です。
// 次に、このループ内で得られた情報を使って $items 配列を構築します。

// 例えば、ユーザーが以下のように入力したとします：

// 商品Aの数量: 3
// 商品Bの数量: 5

// この場合、$_POST['items'] 配列は次のようになります：

// php
// Copy code
// $_POST['items'] = [
//     ['id' => 1, 'quantity' => 3],
//     ['id' => 2, 'quantity' => 5]
// ];
// ループを実行すると、最初の要素 ['id' => 1, 'quantity' => 3] が取り出されます。そのとき、$item['id'] の値は 1 であり、$item['quantity'] の値は 3 です。

// この情報を使って、連想配列 $items を次のように構築します：

// php
// Copy code
// $items = [
//     1 => ['quantity' => 3], // 商品ID 1の商品Aを3個購入
// ];
// 次に、2番目の要素 ['id' => 2, 'quantity' => 5] が取り出されます。そのとき、$item['id'] の値は 2 であり、$item['quantity'] の値は 5 です。

// この情報を使って、連想配列 $items を次のように更新します：

// php
// Copy code
// $items = [
//     1 => ['quantity' => 3], // 商品ID 1の商品Aを3個購入
//     2 => ['quantity' => 5], // 商品ID 2の商品Bを5個購入
// ];
// 以上のようにして、$_POST['items'] 配列内の各要素を順番に処理し、商品IDをキーとして商品の数量情報を $items 配列に格納することで、ユーザーが入力した商品ごとの数量情報が $items 配列に整理されます。





