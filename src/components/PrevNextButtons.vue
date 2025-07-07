<script setup lang="ts">
import { defineProps } from 'vue'
import IconButton from '@/basics/IconButton.vue'

const props = defineProps<{
  prevId: number
  nextId: number
}>()
</script>

<template>
  <div class="nav-wrapper">
    <template v-if="props.prevId !== 0">
      <router-link :to="`/article/${props.prevId}`">
        <IconButton
          label="前の記事"
          icon-class="fa-solid fa-arrow-left"
          background-color="#cccccc"
        />
      </router-link>
    </template>
    <template v-else>
      <div class="disabled-button">
        <IconButton
          label="前の記事なし"
          icon-class="fa-solid fa-ban"
          background-color="#eeeeee"
        />
      </div>
    </template>

    <template v-if="props.nextId !== 0">
      <router-link :to="`/article/${props.nextId}`">
        <IconButton
          label="次の記事"
          icon-class="fa-solid fa-arrow-right"
          background-color="#cccccc"
        />
      </router-link>
    </template>
    <template v-else>
      <div class="disabled-button">
        <IconButton
          label="記事はありません"
          icon-class="fa-solid fa-ban"
          background-color="#cccccc"
        />
      </div>
    </template>
  </div>
</template>

<style scoped>
.nav-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 16px;
}

.disabled-button {
  pointer-events: none; /* クリック無効 */
  opacity: 0.5; /* 視覚的に無効化 */
}
</style>
