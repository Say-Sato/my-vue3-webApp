<!-- ArticleCatchPage 画像ギャラリー（1ファイル版） -->
<script setup lang="ts">
import { defineProps } from 'vue'
import type { Postvalidation } from '@/types/PostResponse'

const props = defineProps<{ posts: Postvalidation[] }>()

// タイトルを9文字で省略
const truncatedTitle = (illust_title: string) =>
  illust_title.length > 9 ? illust_title.slice(0, 9) + '…' : illust_title
</script>

<template>
  <div class="gallery-container">
    <div v-for="post in props.posts" :key="post.illust_id" class="card">
      <router-link :to="`/posts/${post.illust_id}`">
        <div class="image-wrapper">
          <img
            :src="post.thumbnail_url"
            :alt="truncatedTitle(post.illust_title)"
            class="card-image"
          />
          <div class="label-container">
            <div v-if="post.R18" class="blur-label">R18</div>
            <div v-if="post.public" class="private-label">非公開</div>
          </div>
        </div>
      </router-link>
    </div>
  </div>
</template>

<style scoped>
.label-container {
  position: absolute;
  top: 8px;
  left: 8px;
  display: flex;
  gap: 4px;
  pointer-events: none;
}
.blur-label {
  background: rgba(220, 20, 60, 0.7); /* クリムゾン系の赤 */
  color: white;
  font-size: 12px;
  padding: 2px 6px;
  border-radius: 4px;
}

.private-label {
  background: rgba(0, 0, 0, 0.6);
  color: white;
  font-size: 12px;
  padding: 2px 6px;
  border-radius: 4px;
}

.gallery-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* デフォルトは4列 */
  gap: 16px;
  padding: 16px 0;
}

.card {
  position: relative; /* これ必須！ */
}

.image-wrapper {
  position: relative; /* ラベルを画像に重ねたいならここにも付ける */
}

.card:hover {
  transform: translateY(-4px);
}

.card-image {
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
}

.card-body {
  padding: 8px;
}

.card-title {
  font-size: 14px;
  font-weight: 600;
  margin: 4px 0;
}

a {
  color: inherit;
  text-decoration: none;
}

/* スマホ対応（1100px以下は2列表示） */
@media screen and (max-width: 800px) {
  .gallery-container {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}
</style>
