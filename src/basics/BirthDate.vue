<script setup lang="ts">
import { ref, computed, watch } from 'vue'

const props = defineProps<{
  modelValue: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const year = ref('')
const month = ref('')
const day = ref('')

// ✅ props（modelValue）から年月日に初期値を反映
watch(
  () => props.modelValue,
  newVal => {
    if (newVal && newVal.includes('-')) {
      const [y, m, d] = newVal.split('-')
      year.value = y
      month.value = String(Number(m))
      day.value = String(Number(d))
    }
  },
  { immediate: true },
)

// ✅ 年月日が変わったら親へemit
watch([year, month, day], () => {
  const y = year.value
  const m = month.value.padStart(2, '0')
  const d = day.value.padStart(2, '0')
  if (y && m && d) {
    emit('update:modelValue', `${y}-${m}-${d}`)
  }
})

// 月ごとの日数を計算
const daysInMonth = computed(() => {
  const y = parseInt(year.value)
  const m = parseInt(month.value)
  if (!y || !m) return []
  return Array.from({ length: new Date(y, m, 0).getDate() }, (_, i) => i + 1)
})

// 年リスト（1900〜現在）
const currentYear = new Date().getFullYear()
const years = Array.from({ length: currentYear - 1900 + 1 }, (_, i) => 1900 + i)
</script>

<template>
  <div class="birthday-select">
    <select v-model="year">
      <option disabled value="">年</option>
      <option v-for="y in years" :key="y" :value="String(y)">{{ y }}</option>
    </select>

    <select v-model="month">
      <option disabled value="">月</option>
      <option v-for="m in 12" :key="m" :value="String(m)">{{ m }}</option>
    </select>

    <select v-model="day">
      <option disabled value="">日</option>
      <option v-for="d in daysInMonth" :key="d" :value="String(d)">
        {{ d }}
      </option>
    </select>
  </div>
</template>

<style scoped>
.birthday-select select {
  padding: 10px 16px; /* ← パディングを大きめに */
  border-radius: 15px; /* ← 角を丸く */
  border: 1px solid #ccc;
  font-size: 16px;
  margin-right: 8px; /* セレクト同士に少し間隔 */
  background-color: #fff;
  cursor: pointer;
}

.birthday-select select:focus {
  outline: none;
  border-color: #66aaff; /* フォーカス時の色を青系に */
  box-shadow: 0 0 4px rgba(102, 170, 255, 0.6);
}
</style>
