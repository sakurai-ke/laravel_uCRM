<script setup>
import { Inertia } from '@inertiajs/inertia'
defineProps({
    // Controller側のshowメソッドの配列で指定したキー（id)を渡す。
    // 右側には型を指定すること
    id : String,
    // 1件だけ取得する際はArrayではなくObjectとすること（Arrayだとデベロッパーツールでエラーが発生する）
    bloggg: Object
})

const deleteConfirm = id => {
// JSのテンプレート構文 バックスラッシュで囲む 変数箇所を${}で囲む
Inertia.delete(`/inertia/${id}`, {
onBefore: () => confirm('本当に削除しますか?')
})

}

</script>
<template>
{{ id }}<br> 
{{ bloggg.title }}<br>
<button @click="deleteConfirm(bloggg.id)">削除</button>
</template>