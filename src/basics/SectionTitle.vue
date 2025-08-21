<script setup lang="ts">
import { defineProps } from 'vue'

const props = defineProps<{
  title: string
}>()
</script>

<template>
  <div class="title-container">
    <div class="title">
      <h2>{{ props.title }}</h2>
    </div>
    <div class="title-divider"></div>
  </div>
</template>

<style scoped>
.title {
  font-weight: 400;
  font-size: 20px; /* h2相当 */
  font-style: normal;
  margin-bottom: -10px;
}

.title-divider {
  border-bottom: 2px solid #777;
  margin-bottom: 20px; /* 線の下にも20px間隔 */
}
</style>
