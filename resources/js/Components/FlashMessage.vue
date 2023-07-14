<script setup>
</script>

<!-- 、$page.props.flash.message は、サーバーからクライアントに渡されたデータの中の flash.message プロパティを参照しているという意味
    （$page は Inertia.js のコンポーネント内で使用される特殊な変数） -->
<template>
<div v-if="$page.props.flash.status === 'success'" 
class="bg-blue-300 text-white p-4">
{{ $page.props.flash.message }}
</div>
<div v-if="$page.props.flash.status === 'danger'" 
class="bg-red-300 text-white p-4">
{{ $page.props.flash.message }}
</div>
</template>