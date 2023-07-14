<script setup>
import { computed } from 'vue';

// 詳細不明だがおそらくinertiaの場合どこかの親コンポーネントから（Create.vue？？？）errorsプロパティがわたされている？
const props = defineProps({ 
    errors: Object
})
// import { usePage } from '@inertiajs/inertia-vue3';

// const errors = computed(() => usePage().props.value.errors);

const hasErrors = computed(() => Object.keys(props.errors).length > 0);
</script>

<template>
    <div v-if="hasErrors">
        <div class="font-medium text-red-600">問題が発生しました。</div>

        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
            <li v-for="(error, key) in props.errors" :key="key">{{ error }}</li>
        </ul>
    </div>
</template>