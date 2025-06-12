<!-- ArticlePage.vue -->
<template>
  <div>
    <div v-for="image in images" :key="image.image_id" class="image-container">
      <img :src="image.image_url" alt="記事画像" class="image" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Image } from '@/views/ArticleCatchPage.vue' // 型定義の場所に合わせてね

defineProps<{
  images: Image[]
}>()
</script>

<style scoped>
.image-container {
  position: relative;
  width: 95%;
  height: 600px;
  display: block;
  overflow: hidden;
  background-color: #f0f0f0;
  margin: 0 auto 24px;
}

.image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  object-position: center;
}
</style>
