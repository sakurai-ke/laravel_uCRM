<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue' // リアクティブ
const newTitle = ref('')
const newContent = ref('')
</script>

<template>
Inertiaテストです。<br>
<a href="/">aタグでWelcomeに移動</a><br>
<Link href="/">LinkでWelcomeに移動</Link><br>
<Link :href="route('inertia.index')">名前付きルートの確認です</Link><br>
<!-- 通常はv-forでidを指定できるようにするのが一般的、 
（リンクコンポーネントの場合は{id:50}と記述する。通常のlaravelでは[d:50]と記述する）-->
<Link :href="route('inertia.show',{id:50})">ルートパラメータのテストです</Link><br>

<input type="text" name="newTitle" v-model="newTitle">{{ newTitle }}<br>
<input type="text" name="newContent" v-model="newContent">{{ newContent }}<br>
<Link as="button" method="post" :href="route('inertia.store')" :data="{ title:
newTitle, content: newContent}">保存テスト</Link>
</template>