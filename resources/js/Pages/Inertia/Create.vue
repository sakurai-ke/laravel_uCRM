<script setup>
import { reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia'
// バリデーションエラーを使用するためBreezeValidationErrorsファイルを子コンポーネントとして使用するため読み込む
import BreezeValidationErrors from '@/Components/ValidationErrors.vue'

// defineProps関数を使用して、errorsという名前のプロパティを定義しています。これは、コンポーネントの親コンポーネントから渡されるエラーオブジェクトを受け取るためのプロパティです。
// 具体的には、親コンポーネントで <ComponentName :errors="errorsData" /> のようにコンポーネントを使用する場合、errorsDataというエラーオブジェクトが親コンポーネントからプロパティとして渡されます。
// そして、子コンポーネントの defineProps の引数に { errors: Object } と指定することで、errors プロパティを受け取ることができます。
// これにより、子コンポーネント内で errors プロパティにアクセスすることができ、フォームの入力エラーメッセージを保持するオブジェクトとして利用することができます。
// 例えば、<div v-if="errors.title">{{ errors.title }}</div> の部分では、errors.title の値が存在する場合にエラーメッセージを表示するため、errors プロパティに
// 渡されたエラーオブジェクトの title プロパティが参照されています。

// 詳細不明だがおそらくinertiaの場合どこかの親コンポーネントからerrorsプロパティがわたされているらしい
defineProps({
errors: Object
})

// オブジェクトなのでreactiveで指定
const form = reactive({
title: null,
content: null
})

// 移行先のURLと送る情報を指定,formは上記でreactiveで指定している情報
const submitFunction = () => {
    // '/inertia'はweb.phpで指定したpostメソッドの際の移行先
Inertia.post('/inertia', form)
// Inertia.post((route('inertia.store')), form)
}

</script>


<template>
    <!-- バリデーションエラーを使用するためBreezeValidationErrorsファイルを子コンポーネントとして使用する -->
<BreezeValidationErrors :errors="errors" />
    <!-- formタグはそのまま使用するとページを全て再読み込みする仕様となっている、シングルページアプリケーション
    ではなくなってしまうので、全部読み込むのを防ぐためにpreventというオプションを記述する必要がある
    @submit.preventはv-onの省略形で記述している　-->
<form @submit.prevent="submitFunction">
    <!-- 上記のform.titleとinputタグの値が双方向バインディングされておりどちらかの値が変更されるともう一方も自動的に反映される -->
<input type="text" name="title" v-model="form.title">
<div v-if="errors.title">{{ errors.title }}</div>
<input type="text" name="content" v-model="form.content">
<div v-if="errors.content">{{ errors.content }}</div>
<button>送信</button>
</form>
</template>