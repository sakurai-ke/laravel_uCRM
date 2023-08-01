<script setup>
import { Chart, registerables } from "chart.js";
import { BarChart } from "vue-chart-3";
import { reactive, computed } from "vue"

// Analysis.vueからdataという名前のプロパティをpropsとして受け取っています。props.dataは、親コンポーネントで定義されたデータを表します。
const props = defineProps({
  "data" : Object
})

// props：definePropsによって親コンポーネントから受け取ったデータを格納するオブジェクトです。propsはreactiveでなく、通常のJavaScript
// オブジェクトですが、Vue.jsが自動的にリアクティブに変換します。親コンポーネントから受け取ったデータはprops.dataでアクセスできるようになります。
// computed：Vue.jsのcomputed関数を使って計算プロパティを定義します。computed関数は、コンポーネントのリアクティブデータが変更されると自動的に再評価される。
// computed(() => props.data.labels)：この計算プロパティは、props.data.labelsを返す無名関数です。props.data.labelsは親コンポーネント
// から受け取ったデータオブジェクトのlabelsプロパティを指します。props.data.labelsはformオブジェクトのstartDateやendDateなどのデータとして使われることを想定している。
const labels = computed(() => props.data.labels)
const totals = computed(() => props.data.totals)

Chart.register(...registerables);

const barData = reactive({
  labels: labels,
  datasets: [
    {
      label: '売上',
      data: totals,
      backgroundColor: "rgb(75, 192, 192)",
      tension: 0.1,
    }
  ]
})

</script>
<template>
<div v-show="props.data">
    <BarChart :chartData="barData" />
  </div>
</template>
