<script setup>
import { getToday } from '@/common' // app.js/common.jsファイルをインポート（購入日の初期値を当日とするため）
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, reactive, ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import MicroModal from '@/Components/MicroModal.vue'

// PurchaseControllerからデータを受け取る
const props = defineProps({ 
    // 'customers': Array ,
    'items': Array,
    errors: Object
})


// それぞれの商品情報を表示させる
// onMountedでページ読み込み後 即座に実行。getToday()関数の実行結果をform.dateに代入、またinputタグのv-modelでバインディングさせる
onMounted(() => { 
form.date = getToday()
// PurchaseControllerから受け取ったitemsデータをひとつづつ処理
props.items.forEach( item => { // 配列を1つずつ処理
// 下記で新たに定義したitemListの中に商品情報(id、名前、金額、数量、小計）を入れる
// itemListはrefで宣言されているため通常は.valueを付加する
// 配列に1つずつ追加
itemList.value.push({ 
  // 下記のtbodyで選択したデータitemListに追加される。
id: item.id, name: item.name, price: item.price, quantity: 0 })
})
})

const quantity = [ "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"] // option用

// propsのままだと変更できないので?新たに配列を作って追加
// 販売中のItemをv-forで全て表示 数量は初期値0
const itemList = ref([]) // リアクティブな配列を準備

// date: 購入の日付を保持するプロパティです。初期値は null で、ユーザーがフォームに入力した日付がここに設定されます。
// customer_id: 顧客のIDを保持するプロパティです。初期値は null で、ユーザーが選択した顧客のIDがここに設定されます。
// status: 購入のステータスを表すプロパティです。初期値は true で、デフォルトでは購入が有効（true）となります。
// itemsss: 購入する商品の情報を保持する配列プロパティです。各要素は、購入する商品のID (item_id) と数量 (quantity) を
// 持つオブジェクトとして格納されます。初期値は空の配列 [] です。この form オブジェクトは、Vueコンポーネント内でフォームの
// データを管理するために使用されます。ユーザーがフォームを操作すると、データがリアクティブに更新され、フォームに入力された値や
// 選択された商品の情報が form オブジェクトに格納されます。それによって、storePurchase メソッド内で form オブジェクトに
// 含まれるデータを使用して非同期リクエストをサーバーに送信し、購入データを保存することができます。
const form = reactive({ 
    date: null,
    customer_id: null,
    status: true,
    // itemsにはitem.idとitem.quantityを入れる（それぞれ複数なので配列で代入）
    itemsss: []
    })

// 全ての商品の合計金額を算出
// itemListにはitem.id、item.name、item.price、quantityの情報が入っている
const totalPrice = computed(() => {
  let total = 0
  // 上記のonMounted関数から渡されたitemListの中のitem.price、quantityの値から合計値を算出
  // itemListはrefで宣言されているのでitemList.valueと記述している
  itemList.value.forEach( item => {
    total += item.price * item.quantity
  })
  return total
})

// 数量0個以上の商品について配列itemsにitem.idと数量を入れて、データベースに保存する。
const storePurchase = () => {
  itemList.value.forEach( item => {
    // itemListのデータから数量が0より大きい商品の場合にデータを送る
    if( item.quantity > 0){
      // 上記(reactive）で設定したitemsssにpushしていく
      form.itemsss.push({
        id: item.id,
        quantity: item.quantity
      })
    }
  })
  Inertia.post(route('purchases.store'), form )
}

// Modal.vueファイルからデータを受け取る
const setCustomerId = id => {
  form.customer_id = id
}
</script>

<template>
    <Head title="購入画面" />

<AuthenticatedLayout>
<template #header>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        購入画面
    </h2>
</template>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                        <BreezeValidationErrors class="mb-4" :errors="errors"/>
                    <section class="text-gray-600 body-font relative">
                        <form @submit.prevent="storePurchase">
                        <div class="container px-5 py-8 mx-auto">
                        
                        <div class="lg:w-1/2 md:w-2/3 mx-auto">
                            <div class="flex flex-wrap -m-2">
                            <div class="p-2 w-full">
                                <div class="relative">
                                <label for="date" class="leading-7 text-sm text-gray-600">日付</label>
                                <input type="date" id="date" name="date" v-model="form.date" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                </div>
                            </div>

                            <div class="p-2 w-full">
                                <div class="relative">
                                <label for="customer" class="leading-7 text-sm text-gray-600">会員名</label>
                                <MicroModal @update:customerId="setCustomerId" />
                                </div>
                            </div>
                            
                            <div class="w-full mt-8 mx-auto overflow-auto">
                            <table class="table-auto w-full text-left whitespace-no-wrap">
                            <thead>
                                <tr>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">Id</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">商品名</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">金額</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">数量</th>
                                <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">小計</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in itemList" :key="item.id">
                                <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.id }}</td>
                                <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.name }}</td>
                                <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.price }}</td>
                                <td class="border-b-2 border-gray-200 px-4 py-3">
                                    <select name="quantity" v-model="item.quantity">
                                        <!-- 1〜9の選択肢から選択する （quantityで1〜9を配列で定義している）-->
                                    <option v-for="q in quantity" :value="q">{{ q }}</option>
                                    </select>
                                </td>
                                <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.price * item.quantity }}</td>
                                </tr>
                            </tbody>
                            </table>
                        </div>

                            <div class="p-2 w-full">
                                  <div class="">
                                    <label for="price" class="leading-7 text-sm text-gray-600">合計金額</label><br>
                                    <div class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                      {{ totalPrice }} 円
                                    </div>
                                  </div>
                                </div>
                                
                                <div class="p-2 w-full">
                                  <button class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">登録する</button>
                                </div>
                                
                              </div>
                            </div>
                          </div>
                          </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>