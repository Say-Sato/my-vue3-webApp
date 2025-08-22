<!-- ArticleCatchPage 画像ギャラリー（1ファイル版） -->
<script setup lang="ts">
import { defineProps } from 'vue'
import type { Postvalidation } from '@/types/PostResponse'

const props = defineProps<{ posts: Postvalidation[] }>()
</script>

<template>
  <ul class="image-gallery">
    <li v-for="post in props.posts" :key="post.illust_id" class="image-item">
      <router-link :to="`/posts/edit/${post.illust_id}`">
        <div class="box3">
          <div class="image-wrapper">
            <img
              :src="post.thumbnail_url"
              :alt="post.illust_title"
              class="image"
            />
            <div v-if="post.R18" class="blur-overlay">R18</div>
            <!-- ▼ 非公開ラベルの例（仮で非公開固定表示、動的に切り替える場合は別途条件式が必要） -->
            <div class="private-label">非公開</div>
          </div>
          <!-- ▼ タイトル表示（仮） -->
          <div class="image-title">{{ post.illust_title || 'タイトル仮' }}</div>
        </div>
      </router-link>
    </li>
  </ul>
</template>

<style scoped>
.image-gallery {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  padding: 0;
  margin: 0;
  list-style: none;
}

.image-item {
  width: calc(25% - 10px);
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.image-wrapper {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1;
  overflow: hidden;
}

.image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: filter 0.3s ease;
}

.blur-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.5);
  color: white;
  font-size: 20px;
  font-weight: bold;
  text-align: center;
}

.image-title {
  margin-top: 4px;
  font-size: 14px;
  font-weight: 500;
  text-align: center;
  color: #000;
}

.private-label {
  position: absolute;
  top: 8px;
  left: 8px;
  background: rgba(0, 0, 0, 0.6);
  color: white;
  font-size: 12px;
  padding: 2px 6px;
  border-radius: 4px;
  pointer-events: none;
}

@media screen and (max-width: 800px) {
  .image-item {
    width: calc(50% - 10px);
  }
}
</style>
