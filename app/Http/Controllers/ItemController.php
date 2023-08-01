<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
// inertiaは読み込むこと
use Inertia\Inertia;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(Item::select('id', 'name', 'price', 'is_selling')->get());

        return Inertia::render('Items/Index', [
            // Item::all()でもいいが、不要なデータまで取得してしまうのでselectで取得した方が良い、らしい
            // また、selectで取得する場合は最後に->get()が必要
            'items' => Item::select('id', 'name', 'price', 'is_selling')
            ->get()
        ]);

        // 上記は下記のように記述可能（どちらでもよい）
        // プロパティをいくつかに制限して指定しする場合は最後に->get()を記述すること
        // return文では'items' => $itemsと連想配列で記述すること
        // $items = Item::select('id', 'name', 'price', 'is_selling')->get();
        // return Inertia::render('Items/Index', [
        //     'items' => $items
        // ]);    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Items/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItemRequest $request)
    {
        Item::create([
            'name' => $request->name,
            'memo' => $request->memo,
            'price' => $request->price,
        ]);

        return to_route('items.index')
        ->with([
            'message' => '登録しました。' ,
            'status' => 'success'
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        return Inertia::render('Items/Show',
        [
        'item' => $item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        return Inertia::render('Items/Edit',
        [
        'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateItemRequest  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateItemRequest $request, Item $item)
    {

        // $item->***は設定済みの情報、$request->***はフォームに入力した情報
        // $item->***を$request->***に入れ替える
        // dd($item->name, $request->name);
        $item->name = $request->name;
        $item->memo = $request->memo;
        $item->price = $request->price;
        $item->is_selling = $request->is_selling;
        $item->save();

        return to_route('items.index')
        ->with([
        'message' => '更新しました。',
        'status' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $item->delete();
            return to_route('items.index')
            ->with([
            'message' => '削除しました。',
            'status' => 'danger'
            ]);
    }
}
