<script setup>
// import の後の名称は適当に決める。パスは「@/」を最初に記述するとresource/jsからのパスということを意味する。
// 拡張子「.vue」は記述しないとエラーとなる、らしい
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
<!-- <Label>タイトル</Label> -->
<Input modelValue="初期値です" @update:modelValue="emitTest"></Input>
<!-- <Input></Input> -->
</GuestLayout>
</template>