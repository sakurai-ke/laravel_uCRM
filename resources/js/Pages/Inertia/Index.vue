<script setup>
// import { Link } from '@inertiajs/inertia-vue3'; は、Inertia.jsのVue.js用のパッケージから Link コンポーネントをインポートして、動的なリンクを作成するために使用
import { Link } from '@inertiajs/inertia-vue3';
// blogsは配列を拡張したコレクション型なの、Arraytという形で取得する
defineProps({ blogs: Array})
</script>

<!-- 、$page.props.flash.message は、サーバーからクライアントに渡されたデータの中の flash.message プロパティを参照しているという意味
    （$page は Inertia.js のコンポーネント内で使用される特殊な変数） -->
<template>
    <div v-if="$page.props.flash.message">
    {{ $page.props.flash.message }}
    </div>

<ul>
    <!-- 単数形 in 複数形 :keyもつける (ソートや削除などで順序変わっても状態を保持するため) -->
    <li v-for="blog in blogs" :key="blog.id"> 
        件名: <Link class="text-blue-400" :href="route('inertia.show', {id: blog.id })">{{ blog.title }}</Link>,
        本文: {{ blog.content }}
    </li>
</ul>

ああああああ
</template>