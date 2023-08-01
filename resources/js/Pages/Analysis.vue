<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/vue3';
import { reactive, onMounted } from 'vue'
import { getToday } from '@/common'
import Chart from '@/Components/Chart.vue'
import ResultTable from '@/Components/ResultTable.vue'

// onMounted(() => { ... }): この部分は、コンポーネントがマウント（描画）された後に実行される関数を定義
// コンポーネントがマウントされた後にformオブジェクトのstartDateとendDateが現在の日付で初期化されます。
onMounted(() => {
    form.startDate = getToday()
    form.endDate = getToday()
})

// startDate: 開始日を保持するプロパティ。
// endDate: 終了日を保持するプロパティ。
// type: 'perDay',：日別のデータ分析を行うようにデフォルト値として 'perDay' を指定。
const form = reactive({
    startDate: null,
    endDate: null,
    type: 'perDay',
    rfmPrms: [
    14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000, 30000
  ],
})

const data = reactive({})

const getData = async () => {
try{
  await axios.get('/api/analysis/', {
    params: {
      startDate: form.startDate,
      endDate: form.endDate,
      type: form.type,
      rfmPrms: form.rfmPrms
    }
  })
  .then( res => {
    // res：axiosを使ってHTTPリクエストを送信して受け取ったサーバーのレスポンスデータを表すオブジェクトです。
    // 一般的なHTTPリクエストは非同期（Asynchronous）で行われるため、結果はPromiseオブジェクトとして返ってきます。
    // res.data：サーバーからのレスポンスデータの本体を表します。具体的には、APIから返されたJSONデータが格納されています。
    // res.data.data：APIから取得したデータのうち、dataというキーに対応する値を表します。ここでのdataは、例えば注文データのリストや分析結果など、APIが提供しているデータの実際の内容に応じて異なります。
// data.data = res.data.data：Vue.jsのリアクティブデータであるdataオブジェクトのdataプロパティに、サーバーから取得したAPIのデータを代入しています。
// これにより、取得したデータがコンポーネント内で利用可能になり、テンプレートで表示や処理を行うことができるようになります。
      data.data = res.data.data
      if(res.data.labels) {data.labels = res.data.labels}
      if(res.data.eachCount) {data.eachCount = res.data.eachCount}
      data.totals = res.data.totals
      data.type = res.data.type
      console.log(res.data)
    })
  } catch (e){
    console.log(e.message)
  }
}

</script>

<template>
    <Head title="データ分析" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                データ分析
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="getData">
                          分析方法<br>
                          <input type="radio" v-model="form.type" value="perDay" checked><span class="mr-4">日別</span>
                          <input type="radio" v-model="form.type" value="perMonth" ><span class="mr-4">月別</span>
                          <input type="radio" v-model="form.type" value="perYear" ><span class="mr-4">年別</span>
                          <input type="radio" v-model="form.type" value="decile" ><span class="mr-4">デシル分析</span>
                          <input type="radio" v-model="form.type" value="rfm" ><span class="mr-4">RFM分析</span>
                          <br>
                          
                          From: <input type="date" name="startDate" v-model="form.startDate">
                            To: <input type="date" name="endDate" v-model="form.endDate"><br>

                          <div v-if="form.type === 'rfm'" class="my-8">
                            <table class="mx-auto">
                              <thead>
                              <tr>
                                <th>ランク</th>
                                <th>R (○日以内)</th>
                                <th>F (○回以上)</th>
                                <th>M (○円以上)</th>
                              </tr>
                              </thead>
                              <tbody>
                              <tr>
                                <td>5</td>
                                <td><input type="number" v-model="form.rfmPrms[0]"></td>
                                <td><input type="number" v-model="form.rfmPrms[4]"></td>
                                <td><input type="number" v-model="form.rfmPrms[8]"></td>
                              </tr>
                              <tr>
                                <td>4</td>
                                <td><input type="number" v-model="form.rfmPrms[1]"></td>
                                <td><input type="number" v-model="form.rfmPrms[5]"></td>
                                <td><input type="number" v-model="form.rfmPrms[9]"></td>
                              </tr>
                              <tr>
                                <td>3</td>
                                <td><input type="number" v-model="form.rfmPrms[2]"></td>
                                <td><input type="number" v-model="form.rfmPrms[6]"></td>
                                <td><input type="number" v-model="form.rfmPrms[10]"></td>
                              </tr>
                              <tr>
                                <td>2</td>
                                <td><input type="number" v-model="form.rfmPrms[3]"></td>
                                <td><input type="number" v-model="form.rfmPrms[7]"></td>
                                <td><input type="number" v-model="form.rfmPrms[11]"></td>
                              </tr>
                              </tbody>
                            </table>
                          
                          </div>

                            <button class="mt-4 flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">分析する</button>
                          </form>
<!-- dataオブジェクトのdataプロパティが真の場合（つまり、data.dataが真の値を持つ場合）、その中身を表示します。data.dataが偽の値（例：空の配列やnull）の場合は、
  <div v-show="data.data">内のコンテンツが非表示になります。 -->
                        <div v-show="data.data">
                          <div v-if="data.type != 'rfm' ">
                            <Chart :data="data" />
                          </div>
                        <ResultTable :data="data" />
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
