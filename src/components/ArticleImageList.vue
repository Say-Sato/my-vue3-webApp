<!-- ArticlePage.vue -->
<script setup lang="ts">
import type { Image } from '@/views/ArticleCatchPage.vue' // 型定義の場所に合わせてね

defineProps<{
  images: Image[]
  // Editについて、親より指定がなければundefined≒falseとする
  Edit?: boolean
}>()
</script>

<template>
  <!-- 編集モード -->
  <div v-if="!Edit">
    <div v-for="image in images" :key="image.image_id" class="image-container">
      <img :src="image.image_url" alt="記事画像" class="image" />
    </div>
  </div>

  <!-- 汎用モード -->
  <div v-else class="preview-scroll">
    <div
      v-for="image in images"
      :key="image.image_id"
      class="image-preview-wrapper"
    >
      <img :src="image.image_url" alt="記事画像" class="thumb" />
    </div>
  </div>
</template>

<style scoped>
.image-container {
  position: relative;
  width: 100%;
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

.preview-scroll {
  display: flex;
  flex-direction: row;
  overflow-x: auto;
  gap: 10px;
  padding: 10px 0;
}

.image-preview-wrapper {
  position: relative;
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  box-sizing: border-box;
}

.thumb {
  height: auto;
  max-height: 100%;
  width: auto;
  object-fit: contain;
  border: 1px solid #ccc;
  flex-shrink: 0;
}
</style>
