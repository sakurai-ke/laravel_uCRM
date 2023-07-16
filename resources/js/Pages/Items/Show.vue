<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { nl2br } from '@/common';
import { Inertia } from '@inertiajs/inertia'

// 上記のコードでは、defineProps関数を使用して、errorsという名前のプロパティを定義しています。これは、コンポーネントの親コンポーネントから渡されるエラーオブジェクトを受け取るためのプロパティです。
// 具体的には、親コンポーネントで <ComponentName :errors="errorsData" /> のようにコンポーネントを使用する場合、errorsDataというエラーオブジェクトが親コンポーネントからプロパティとして渡されます。そして、子コンポーネントの defineProps の引数に { errors: Object } と指定することで、errors プロパティを受け取ることができます。
// これにより、子コンポーネント内で errors プロパティにアクセスすることができ、フォームの入力エラーメッセージを保持するオブジェクトとして利用することができます。
// 例えば、<div v-if="errors.title">{{ errors.title }}</div> の部分では、errors.title の値が存在する場合にエラーメッセージを表示するため、errors プロパティに渡されたエラーオブジェクトの title プロパティが参照されています。

// Controller側のshowメソッドの配列で指定したキー（item)を渡す。
// 右側には型を指定すること（一件だけ受け渡されているので型はObjectとする）
defineProps({
item: Object
})

const deleteItem = id => {
    Inertia.delete(route('items.destroy', {item: id}), {
    onBefore: () => confirm('本当に削除しますか?')
    })
    }

</script>
<template>
    <Head title="商品詳細" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">商品詳細</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <section class="text-gray-600 body-font relative">
                                    <div class="container px-5 py-8 mx-auto">
                                
                                    <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                        <div class="flex flex-wrap -m-2">
                                            <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="name" class="leading-7 text-sm text-gray-600">商品名</label>
                                                <div id="name" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                {{ item.name }}
                                                </div>
                                                </div>
                                            </div>
                                            
                                            <!-- {{ nl2br(item.memo) だとbrタグがそのまま表示されるので、divタグの中にv-html="nl2br(item.memo)"を記述する}} -->
                                            <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="memo" class="leading-7 text-sm text-gray-600">メモ</label>
                                                <div id="memo" v-html="nl2br(item.memo)" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out">
                                                </div>
                                            </div>
                                            </div>

                                            <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="price" class="leading-7 text-sm text-gray-600">商品価格</label>
                                                <div id="price" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ item.price }}
                                                </div>
                                                </div>
                                            </div>

                                            <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="status" class="leading-7 text-sm text-gray-600">商品価格</label>
                                                <div id="status" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                    <span v-if="item.is_selling === 1 ">販売中</span>
                                                    <span v-if="item.is_selling === 0 ">停止中</span>
                                                </div>
                                                </div>
                                            </div>

                                            <div class="p-2 w-full">
                                            <Link as="button" :href="route('items.edit', { item:item.id })" class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">編集する</Link>
                                            </div>
                                        
                                            <div class="mt-20 p-2 w-full">
                                                <button @click="deleteItem(item.id)" class="flex mx-auto text-white bg-red-500 border-0 py-2 px-8 focus:outline-none hover:bg-red-600 rounded text-lg" >削除する</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
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