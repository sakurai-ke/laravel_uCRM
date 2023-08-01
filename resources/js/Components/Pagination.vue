<script setup>
import { Link } from '@inertiajs/vue3';
// defineProps 関数を使って links というプロパティを定義しています。これにより、親コンポーネント（Index.vue）から 
// links プロパティが受け取れるようになります。この links プロパティには、ページネーションリンクに必要な情報が含まれています。
defineProps({ links: Array })
</script>
<template>

    <!-- v-if="links.length > 3": links プロパティの長さが3より大きい場合のみ、ページネーションリンクを表示します。
    これはページネーションリンクが3つ以上ある場合にのみ表示するためです。 -->
<div v-if="links.length > 3">
<div class="flex flex-wrap -mb-1">
<template v-for="(link, index) in links" :key="index">

    <!--<div v-if="link.url === null">: link.url が null の場合、このブロックが実行されます。
    ここでは、link.label の内容が表示されます。link.label はページネーションリンクのテキスト部分を表します。  -->
<div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4
text-gray-400 border rounded"
v-html="link.label" />

<!-- <Link v-else>: link.url が null でない場合、このブロックが実行されます。Link コンポーネントを使って、
    実際のページネーションリンクをレンダリングしています。link.active が true の場合、bg-blue-700 クラスと 
    text-white クラスが適用され、アクティブなページを示すスタイルが適用されます。 
:href="link.url": Link コンポーネントの href 属性には、ページネーションリンクのURLが指定されます。
v-html="link.label": Link コンポーネントの内容として、link.label のHTMLが表示されます。link.label はページネーションリンクのテキスト部分を表します。-->
<Link v-else
class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded
hover:bg-white focus:border-indigo-500 focus:text-indigo-500"

:class="{ 'bg-blue-700 text-white': link.active }" :href="link.url" v-html="
link.label" />
</template>
</div>
</div>
</template>