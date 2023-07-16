<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia'
// バリデーションエラーを使用するためBreezeValidationErrorsファイルを子コンポーネントとして使用するため読み込む
import BreezeValidationErrors from '@/Components/ValidationErrors.vue'

// 上記のコードでは、defineProps関数を使用して、errorsという名前のプロパティを定義しています。これは、コンポーネントの親コンポーネントから渡されるエラーオブジェクトを受け取るためのプロパティです。
// 具体的には、親コンポーネントで <ComponentName :errors="errorsData" /> のようにコンポーネントを使用する場合、errorsDataというエラーオブジェクトが親コンポーネントからプロパティとして渡されます。そして、子コンポーネントの defineProps の引数に { errors: Object } と指定することで、errors プロパティを受け取ることができます。
// これにより、子コンポーネント内で errors プロパティにアクセスすることができ、フォームの入力エラーメッセージを保持するオブジェクトとして利用することができます。
// 例えば、<div v-if="errors.title">{{ errors.title }}</div> の部分では、errors.title の値が存在する場合にエラーメッセージを表示するため、errors プロパティに渡されたエラーオブジェクトの title プロパティが参照されています。

// 詳細不明だがおそらくinertiaの場合どこかの親コンポーネントからerrorsプロパティがわたされているらしい
const props = defineProps({
    item: Object,
    errors: Object
})

// オブジェクトなのでreactiveで指定
// 各フィールドの初期値として propsで受け取った各idのitem情報が設定されています。
const form = reactive({
    id: props.item.id,
    name: props.item.name,
    memo: props.item.memo,
    price: props.item.price,
    is_selling: props.item.is_selling
})

// 移行先のURLと送る情報を指定,formは上記でreactiveで指定している情報。メソッド名は動詞名詞とした方がよい、らしい
const updateItem = id => {
Inertia.put(route('items.update', { item: id}), form)
// URIを指定する記述方法だと下記の通り
// Inertia.put('/items/' + id, form);
}
</script>

<template>
    <Head title="商品編集" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">商品編集</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                    <!-- バリデーションエラーを使用するためBreezeValidationErrorsファイルを子コンポーネントとして使用する -->
                    <BreezeValidationErrors :errors="errors" />
                        <section class="text-gray-600 body-font relative">
                            <!-- 上記で記述されているformのidを指定する -->
                            <form @submit.prevent="updateItem(form.id)">
                                    <div class="container px-5 py-8 mx-auto">
                                
                                    <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                        <div class="flex flex-wrap -m-2">
                                            <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="name" class="leading-7 text-sm text-gray-600">商品名</label>
                                                <input type="text" id="name" name="name" v-model="form.name" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            </div>
                                            
                                            <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="memo" class="leading-7 text-sm text-gray-600">メモ</label>
                                                <textarea id="memo" name="memo" v-model="form.memo" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                            </div>
                                            </div>

                                            <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="price" class="leading-7 text-sm text-gray-600">商品価格</label>
                                                <input type="number" id="price" name="price" v-model="form.price" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            </div>

                                            <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="is_selling" class="leading-7 text-sm text-gray-600">ステータス</label>
                                                <input type="radio" id="is_selling" name="is_selling" v-model="form.is_selling" value="1">
                                                <label class="ml-2 mr-4">販売中</label>
                                                <input type="radio" id="is_selling" name="is_selling" v-model="form.is_selling" value="0">
                                                <label class="ml-2 mr-4">停止中</label>
                                            </div>
                                            </div>


                                            <div class="p-2 w-full">
                                            <button class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">更新する</button>
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

<!-- Inertia.get('/path'): 指定したパスに対してGETリクエストを送信します。このメソッドは、データの取得や表示のために使用されます。
Inertia.put('/path', data): 指定したパスに対してPUTリクエストを送信します。このメソッドは、データの更新や編集のために使用されます。
Inertia.patch('/path', data): 指定したパスに対してPATCHリクエストを送信します。このメソッドもデータの更新や編集に使用されますが、PUTリクエストとは異なり、一部のデータのみを更新する場合に適しています。
Inertia.delete('/path'): 指定したパスに対してDELETEリクエストを送信します。このメソッドは、データの削除を行うために使用されます。 -->