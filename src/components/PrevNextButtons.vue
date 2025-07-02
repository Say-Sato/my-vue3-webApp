<script setup lang="ts">
import { defineProps } from 'vue'
import IconButton from '@/basics/IconButton.vue' // 子コンポーネントのパスに合わせてね！

const props = defineProps<{
  prevId: number
  nextId: number
}>()
</script>

<template>
  <div class="nav-wrapper">
    <router-link :to="`/article/${props.prevId}`">
      <IconButton
        label="前の記事"
        icon-class="fa-solid fa-arrow-left"
        background-color="#cccccc"
      />
    </router-link>
    <router-link :to="`/article/${props.nextId}`">
      <IconButton
        label="次の記事"
        icon-class="fa-solid fa-arrow-right"
        background-color="#cccccc"
      />
    </router-link>
  </div>
</template>

<style scoped>
.nav-wrapper {
  display: flex;
  justify-content: center; /* 横方向中央揃え */
  align-items: center; /* 縦方向も中央揃え（念のため） */
  gap: 20px; /* ボタンの間隔20px */
  margin-top: 16px; /* 上に余白（お好みで） */
}
</style>
