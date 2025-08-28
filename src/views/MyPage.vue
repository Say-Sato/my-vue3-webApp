<script setup lang="ts">
import { ref, computed } from 'vue'

// cは必須入力項目
const c = ref<number | null>(null)
const a = ref<number | null>(null)
const b = ref<number | null>(null)
const resultAvailable = ref<boolean>(false) // 計算結果が利用可能かどうか

// プルダウンの使用フラグ
const useDropdown = ref<boolean>(false)

// プルダウンで選択可能なスコアのオプション
const scoreOptions = [9800000, 9900000, 9950000, 10000000]

// 計算が可能かどうかをチェックするためのcomputedプロパティ
const canCalculate = computed(() => {
  return c.value !== null && (a.value !== null || b.value !== null)
})

// 小数点2位で繰り上げる関数
const roundUpToTwoDecimalPlaces = (value: number): number => {
  return Math.ceil(value * 10) / 10
}

// 計算を実行する関数
const performCalculation = () => {
  if (a.value !== null && c.value !== null) {
    // aが入力されている場合、bを計算
    b.value = calculateBFromA(a.value, c.value)
  } else if (b.value !== null && c.value !== null) {
    // bが入力されている場合、aを計算
    a.value = calculateAFromB(b.value, c.value)
  }
  resultAvailable.value = true // 計算結果が利用可能
}

// aとcの値からbを計算する関数
//ポテンシャル上昇の条件分岐
const calculateBFromA = (aValue: number, cValue: number): number | null => {
  if (cValue === aValue + 2.0) {
    // 条件1: b = PM の場合
    return 10000000
  } else if (cValue <= aValue + 1.0 && cValue < aValue + 2.0) {
    // 条件2: EX <= b < PM の場合
    return (cValue - aValue - 1.0) * 200000 + 9800000
  } else if (cValue > aValue + 2.0) {
    // 条件3: 譜面定数がポテンシャルより2.0以上低い場合
    return 0
  } else {
    // 条件3: 上記以外 (b < EX以下)
    return (cValue - aValue) * 300000 + 9500000
  }
}

// bとcの値からaを計算する関数
const calculateAFromB = (bValue: number, cValue: number): number => {
  let result: number

  if (bValue >= 10000000) {
    // 条件1: b >= 10000000 の場合
    result = cValue - 2.0
  } else if (bValue >= 9800000 && bValue < 10000000) {
    // 条件2: 9800000 <= b < 10000000 の場合
    result = cValue - 1.0 - (bValue - 9800000) / 200000
  } else {
    // 条件3: 上記以外 (b < 9800000)
    result = cValue - (bValue - 9500000) * 300000
  }

  // 小数点2位で繰り上げ
  return roundUpToTwoDecimalPlaces(result)
}

// フォームの内容をリセットする関数
const resetForm = () => {
  c.value = null
  a.value = null
  b.value = null
  resultAvailable.value = false
  useDropdown.value = false
}
</script>

<template>
  <div>
    <h1>計算フォーム</h1>
    <!-- cの入力フォーム（必須） -->
    <div>
      <label for="c">あなたのポテンシャルを入力してください（必須）:</label>
      <input type="number" v-model.number="c" placeholder="例：12.65" />
    </div>
    <!-- aの入力フォーム -->
    <div>
      <label for="a">プレイする曲の譜面定数を入力してください:</label>
      <input
        type="number"
        v-model.number="a"
        :disabled="b !== null"
        placeholder="例：11.7"
      />
    </div>
    <!-- bの入力フォーム -->
    <div>
      <label for="b">想定スコアを入力してください:</label>
      <!-- 手入力とプルダウンの選択を切り替えるチェックボックス -->
      <div>
        <input type="checkbox" v-model="useDropdown" />
        <label>プルダウンから選択</label>
      </div>
      <div v-if="useDropdown">
        <!-- プルダウン選択 -->
        <select v-model.number="b" :disabled="a !== null">
          <option v-for="option in scoreOptions" :key="option" :value="option">
            {{ option }}
          </option>
        </select>
      </div>
      <div v-else>
        <!-- 手入力 -->
        <input
          type="number"
          v-model.number="b"
          :disabled="a !== null"
          placeholder="例：9800000"
        />
      </div>
    </div>
    <!-- 計算を実行するボタン -->
    <div>
      <button @click="performCalculation" :disabled="!canCalculate">
        計算を実行
      </button>
    </div>
    <!-- フォームのリセットボタン -->
    <div>
      <button @click="resetForm">フォームをリセット</button>
    </div>
    <!-- 計算結果の表示 -->
    <div v-if="resultAvailable">
      <p>計算結果:</p>
      <p>ポテンシャル: {{ c }}</p>
      <p>譜面定数: {{ a }}</p>
      <p>
        想定スコア:
        <span v-if="b === 10000000"> - PM</span>
        <span v-else-if="b === 0"> - ポテンシャルは上がりません</span>
        <span v-else>{{ b }}</span>
      </p>
    </div>
  </div>
</template>

<style scoped>
/* スタイルは必要に応じて調整してください */
div {
  margin: 20px;
}

label {
  margin-right: 10px;
}

button {
  margin-top: 20px;
}
</style>
