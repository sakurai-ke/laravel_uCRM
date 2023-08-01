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

// バリデーションエラーのための記述
// 詳細不明だがControllerにてvalidateの設定をするとerrorが受け渡されるようになっている、らしい
defineProps({
errors: Object
})

// 下記の「const form~」、「const storeItem~」はデータ保存するために記述する
// オブジェクトなのでreactiveで指定。
// 各フィールドの初期値として null が設定されています。
// この form オブジェクトは、フォームの入力フィールド（v-model ディレクティブを使用してバインドされたフィールド）と双方向のデータバインディングを行うために使用されます。
// つまり、ユーザーがフォームのフィールドに入力を行うと、form オブジェクトの対応するプロパティ（name、memo、price）に入力された値が反映されます。
// 同様に、form オブジェクトの各プロパティの値が変更されると、入力フィールドの値も更新されます。
const form = reactive({
    name: null,
    memo: null,
    price:null
})

// 移行先のURLと送る情報を指定,formは上記でreactiveで指定している情報。メソッド名（ここではstoreItem）は動詞名詞とした方がよい、らしい
const storeItem = () => {
Inertia.post('/items', form)
}
</script>

<template>
    <Head title="商品登録" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">商品登録</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                    <!-- 親コンポーネントであるCreate.vueから子コンポーネントであるBreezeValidationErrors.vueにエラーオブジェクトをpropsとして渡しています。
                    このエラーオブジェクトにはバリデーションエラーの情報が含まれており、フォームの入力内容に対するバリデーションが失敗した場合にエラーメッセージを表示するために使われます。 -->
                    <BreezeValidationErrors :errors="errors" />
                        <section class="text-gray-600 body-font relative">
                            <!-- Vue.jsでは、@submit ディレクティブを使用してこの submit イベントを監視し、対応するメソッドを実行する
                                Vue.jsの @submit.prevent ディレクティブを使用することで、フォームのデフォルトの送信動作をキャンセルしてページの再読み込みを防ぐ -->
                            <form @submit.prevent="storeItem">
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
                                            <button class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">商品登録</button>
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