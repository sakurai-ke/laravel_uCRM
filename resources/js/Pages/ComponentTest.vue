<script setup>
// import の後の名称は適当に決める。パスは「@/」を最初に記述するとresource/jsからのパスということを意味する。
import Label from '@/Components/InputLabel.vue'
import Input from '@/Components/TextInput.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const emitTest = e => console.log(e)

</script>
<template>
<GuestLayout>
ああああああ
<!-- もしvalueに値があればそのvalueを表示、valueが設定されていなければLabelタグ内の記述を表示させる -->
<Label value="件名">タイトル</Label>
<Input modelValue="初期値です" @update:modelValue="emitTest"></Input>
</GuestLayout>
</template>