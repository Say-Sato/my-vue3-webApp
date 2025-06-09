<script setup lang="ts">
import { ref, computed, watch } from 'vue'

// ✅ v-model用に props を受け取る
const props = defineProps<{
  modelValue: string
}>()

// ✅ v-model用に emit を定義
const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

// 🐣 年月日を個別に管理（初期は空）
const year = ref('')
const month = ref('')
const day = ref('')

// ✅ props（modelValue）から年月日に初期値を反映
if (props.modelValue) {
  const [y, m, d] = props.modelValue.split('-')
  year.value = y ?? '2000'
  month.value = m ?? '1'
  day.value = d ?? '1'
}

// ✅ 値が変わったら即emit（submitしなくてもv-modelが反映される）
watch([year, month, day], () => {
  const y = year.value
  const m = month.value.padStart(2, '0')
  const d = day.value.padStart(2, '0')
  if (y && m && d) {
    emit('update:modelValue', `${y}-${m}-${d}`)
  }
})

// 🔢 月ごとの日数を計算
const daysInMonth = computed(() => {
  const y = parseInt(year.value)
  const m = parseInt(month.value)
  if (!y || !m) return []
  return Array.from({ length: new Date(y, m, 0).getDate() }, (_, i) => i + 1)
})

// 📅 年リスト（1900〜今年）
const currentYear = new Date().getFullYear()
const years = Array.from({ length: currentYear - 1900 + 1 }, (_, i) => 1900 + i)

// 🧪 手動送信したいならsubmit関数はそのままでもOK（オマケ）
const submit = () => {
  const formatted = `${year.value}-${month.value.padStart(2, '0')}-${day.value.padStart(2, '0')}`
  alert(`送信する日付: ${formatted}`)
}
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

    <button @click="submit">送信</button>
  </div>
</template>
