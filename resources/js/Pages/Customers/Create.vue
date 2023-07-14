<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia'
// バリデーションエラーを使用するためBreezeValidationErrorsファイルを子コンポーネントとして使用するため読み込む
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Core as YubinBangoCore } from "yubinbango-core2";

// 上記のコードでは、defineProps関数を使用して、errorsという名前のプロパティを定義しています。これは、コンポーネントの親コンポーネントから渡されるエラーオブジェクトを受け取るためのプロパティです。
// 具体的には、親コンポーネントで <ComponentName :errors="errorsData" /> のようにコンポーネントを使用する場合、errorsDataというエラーオブジェクトが親コンポーネントからプロパティとして渡されます。そして、子コンポーネントの defineProps の引数に { errors: Object } と指定することで、errors プロパティを受け取ることができます。
// これにより、子コンポーネント内で errors プロパティにアクセスすることができ、フォームの入力エラーメッセージを保持するオブジェクトとして利用することができます。
// 例えば、<div v-if="errors.title">{{ errors.title }}</div> の部分では、errors.title の値が存在する場合にエラーメッセージを表示するため、errors プロパティに渡されたエラーオブジェクトの title プロパティが参照されています。

// 詳細不明だがおそらくinertiaの場合どこかの親コンポーネントからerrorsプロパティがわたされているらしい
defineProps({
errors: Object
})

const form = reactive({
  name: null, kana: null, tel: null, email: null, postcode: null,
  address: null, birthday: null, gender: null, memo: null
})

// 郵便番号で住所検索。数字を文字に変換 第１引数が郵便番号、第２がコールバックで引数に住所
// 郵便番号はStringで文字型にしないとダメ、らしい
const fetchAddress = () => {
  new YubinBangoCore(String(form.postcode), (value) => {
    console.log(value)
    form.address = value.region + value.locality + value.street
  })
}

// 移行先のURLと送る情報を指定,formは上記でreactiveで指定している情報。メソッド名は動詞名詞とした方がよい、らしい
const storeCustomer = () => {
  Inertia.post('/customers', form)
}
</script>

<template>
    <Head title="顧客登録" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">顧客登録</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                    <!-- バリデーションエラーを使用するためBreezeValidationErrorsファイルを子コンポーネントとして使用する -->
                    <BreezeValidationErrors :errors="errors" />
                        <section class="text-gray-600 body-font relative">
                            <form @submit.prevent="storeCustomer">
                                    <div class="container px-5 py-8 mx-auto">
                                
                                    <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                        <div class="flex flex-wrap -m-2">

                                            <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="name" class="leading-7 text-sm text-gray-600">顧客名</label>
                                                    <input type="text" id="name" name="name" v-model="form.name" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                </div>
                                                </div>

                                                <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="kana" class="leading-7 text-sm text-gray-600">顧客名カナ</label>
                                                    <input type="text" id="kana" name="kana" v-model="form.kana" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                </div>
                                                </div>

                                                <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="tel" class="leading-7 text-sm text-gray-600">電話番号</label>
                                                    <input type="tel" id="tel" name="tel" v-model="form.tel" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                </div>
                                                </div>

                                                <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="email" class="leading-7 text-sm text-gray-600">メールアドレス</label>
                                                    <input type="email" id="email" name="email" v-model="form.email" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                </div>
                                                </div>

                                                <!-- @changeについて、内容が変更されてカーソルが外れた時に実行する -->
                                                <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="postcode" class="leading-7 text-sm text-gray-600">郵便番号</label>
                                                    <input type="number" id="postcode" name="postcode" @change="fetchAddress" v-model="form.postcode" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                </div>
                                                </div>

                                                <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="address" class="leading-7 text-sm text-gray-600">住所</label>
                                                    <input type="text" id="address" name="address" v-model="form.address" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                </div>
                                                </div>

                                                <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="birthday" class="leading-7 text-sm text-gray-600">誕生日</label>
                                                    <input type="date" id="birthday" name="birthday" v-model="form.birthday" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                </div>
                                                </div>

                                                <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label class="leading-7 text-sm text-gray-600">性別</label>
                                                    <input type="radio" id="gender0" name="gender" v-model="form.gender" value="0" >
                                                    <label for="gender0" class="ml-2 mr-4">男性</label>
                                                    <input type="radio" id="gender1" name="gender" v-model="form.gender" value="1" >
                                                    <label for="gender1" class="ml-2 mr-4">女性</label>
                                                    <input type="radio" id="gender2" name="gender" v-model="form.gender" value="2" >
                                                    <label for="gender2" class="ml-2 mr-4">その他</label>
                                                </div>
                                                </div>
                                                
                                                <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="memo" class="leading-7 text-sm text-gray-600">メモ</label>
                                                    <textarea id="memo" name="memo" v-model="form.memo" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                                </div>
                                                </div>

                                            <div class="p-2 w-full">
                                            <button class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">顧客登録</button>
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