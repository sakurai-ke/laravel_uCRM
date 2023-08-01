<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import Pagination from '@/Components/Pagination.vue'
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia';

// CustomerController.phpについて->get()を->paginate(50)に変更した場合customers情報（顧客情報）を受け取るには
// 形式をObjectで受け取るようにすること（->get()で受け取る際はarrayで受け取る）
// 検索ボタンクリック→searchCustomersメソッドが動作→フォームで検索された結果の内容をCustomerControllerから受け取っている？？？
defineProps({ customers: Object })

// Vue 3のComposition APIで使われるref関数を使って、searchという変数を宣言しています。ref関数はリアクティブな変数を作成し、
// 変数の値が変更されるとVueがそれを検知して自動的に再レンダリングします。
const search = ref('')
// ref の値を取得するには .valueが必要
// 検索ボタンがクリックされた時に実行される関数です。Inertia.get()はInertia.jsを使ってAPIリクエストを送信します。
// route('customers.index', { search: search.value })は、customers.indexという名前のルートに対して、searchという
// クエリパラメータとしてsearch.value（検索文字列）を付加しています。
const searchCustomers = () => {
Inertia.get(route('customers.index', { search: search.value }))
}
</script>

<template>
    <Head title="顧客一覧" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">顧客一覧</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <section class="text-gray-600 body-font">
                            <div class="container px-5 py-8 mx-auto">
                                <!-- フラッシュメッセージをコンポーネントで呼び出す -->
                                <FlashMessage />
                                <div class="flex pl-4 my-4 lg:w-2/3 w-full mx-auto">
                                    <div>
                                        <!-- v-model="search"を使ってsearch変数とバインディングしています。ボタンをクリックするとsearchCustomers関数が実行されます。 -->
                                        <input type="text" name="search" v-model="search">
                                        <button class="bg-blue-300 text-white py-2 px-2" @click="searchCustomers">検索</button>
                                    </div>
                                    <Link as="button" :href="route('customers.create')" class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">顧客登録</Link>
                                </div>

                                <div class="lg:w-2/3 w-full mx-auto overflow-auto">
                                <table class="table-auto w-full text-left whitespace-no-wrap">
                                    <thead>
                                    <tr>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">Id</th>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">氏名</th>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">カナ</th>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">電話番号</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <!-- オブジェクト形式で渡ってきたcustomersデータのdataの中にidとかnameとかkanaの情報があり、
                                        それら一つ一つ処理する -->
                                    <tr v-for="customer in customers.data"  :key="customer.id">
                                        <td class="border-b-2 border-gray-200 px-4 py-3">
                                            {{ customer.id }}
                                        </td>
                                        <td class="border-b-2 border-gray-200 px-4 py-3">{{customer.name}}</td>
                                        <td class="border-b-2 border-gray-200 px-4 py-3">{{customer.kana}}</td>
                                        <td class="border-b-2 border-gray-200 px-4 py-3">{{customer.tel}}</td>

                                    </tr>
                                    
                                    </tbody>
                                </table>
                                </div>
                               
                            </div>
                            <Pagination class="mt-6" :links="customers.links"></Pagination>
                        </section>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
