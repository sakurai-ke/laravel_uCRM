<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $getTest は配列、$getPaginateはオブジェクト、らしい
        // $getTest = Customer::select('id', 'name', 'kana', 'tel')->get();
        // $getPaginate = Customer::select('id', 'name', 'kana', 'tel')->paginate(50);

        // dd($getTest, $getPaginate);

        // ($request->search)について、searchという名前の入力値を取得するための記述
        $customers =
        // $request->search は、クライアントから送信された「search」という名前の検索キーワードを取得する記述です。
        // クライアント側で検索キーワードが指定されていない場合は、この値は null になります。
        // Customer::searchCustomers($request->search) は、Customer モデルに定義された searchCustomers スコープを使用して、
        // 検索条件に基づいて顧客データをフィルタリングしています。前述の通り、$request->search には検索キーワードが含まれているかも
        // しれないので、この検索キーワードを使ってデータを絞り込んでいます。
        // ちなみにsearchはindex.vueの中のInertia.get(route('customers.index', { search: search.value }))のキーのsearchを指しているとのこと。
            Customer::searchCustomers($request->search)
            // クエリにより少ないデータを取得するためのメソッドチェーンです。ここでは、データベースから id, name, kana, tel カラムのみを
            // 取得し、1ページに最大50件のデータを表示するように設定しています。
            ->select('id', 'name', 'kana', 'tel')->paginate(50);
            // Inertia.jsを使用して顧客データの一覧ページを表示するためのコードです。Inertia.jsは、LaravelとVue.jsを連携させるためのツールで、サーバーサイドでデータを取得してクライアントに渡すことで、フロントエンド側でリアクティブなアプリケーションを構築できるようにします。
            // ここでは、'Customers/Index' テンプレートを表示し、その際に顧客データを customers という変数名でテンプレートに渡しています。
        return Inertia::render('Customers/Index', [
            'customers' => $customers
        ]);
        // return Inertia::render('Customers/Index', [
        //     'customers' => Customer::select('id', 'name', 'kana', 'tel')->paginate(50)
        // ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Customers/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        Customer::create([
            'name' => $request->name,
            'kana' => $request->kana,
            'tel' => $request->tel,
            'email' => $request->email,
            'postcode' => $request->postcode,
            'address' => $request->address,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'memo' => $request->memo,
            ]);
            return to_route('customers.index')
            ->with([
            'message' => '登録しました。',
            'status' => 'success'
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
