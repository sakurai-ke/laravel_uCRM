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
        //   各商品の商品をそれぞれ合計した値をsum(subtotal) as totalとして定義している
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
        // purchaseテーブルから一件の購買idを取得して$purchaseに代入
        $purchase = Purchase::find($purchase->id);
        // itemテーブルから'id', 'name', 'price'を取得して$allItemsに代入
        $allItems = Item::select('id', 'name', 'price')
        ->get();
        // 上記２つから新しい配列を作成する???
        $items = [];

        // 一件づつの商品idをチェック
        foreach($allItems as $allItem){
            // 一旦数量の初期値を0とし、中間テーブルに情報があれば更新する
            $quantity = 0;
            foreach($purchase->items as $item){
                if($allItem->id === $item->id){
                    $quantity = $item->pivot->quantity;
                }
            }
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
        $purchase->status = $request->status;
        $purchase->save();

        $items = [];

        foreach($request->items as $item){
            $items = $items + [
                $item['id'] => [
                    'quantity' => $item['quantity']
                ]
            ];
        }

        // dd($items);
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
