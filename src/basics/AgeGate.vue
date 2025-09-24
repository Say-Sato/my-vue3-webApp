<script setup lang="ts">
import { useAgeCheck } from '@/composables/useAgeCheck'

const { calcAge, isAdult } = useAgeCheck()

console.log(calcAge('2000-05-01')) // → 24（2024年基準の場合）
console.log(isAdult('2000-05-01')) // → true
console.log(isAdult('2010-05-01')) // → false
</script>

<template>
  <div></div>
</template>

<style scoped></style>
