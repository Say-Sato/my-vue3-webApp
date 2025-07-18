<script setup lang="ts">
import { defineProps } from 'vue'

export type Image = {
  image_id: number
  image_url: string
  sort_order: number
}

export type PostResponse = {
  id: number
  p_id: number
  title: string
  tags: number[]
  url: string
  body: string
  R18: number
  s_url: string
  p_name: string
  p_photo: string
  images: Image[]
}

// props に tags, images を含むように型定義
const props = defineProps<{ posts: PostResponse[] }>()
</script>

<template>
  <ul class="image-gallery">
    <li v-for="post in props.posts" :key="post.id" class="image-item">
      <router-link :to="`/posts/${post.id}`">
        <div class="box3">
          <div class="image-wrapper">
            <img
              :src="post.s_url"
              :alt="post.title"
              class="image"
              :class="{ blurred: post.R18 }"
            />
            <div v-if="post.R18" class="blur-overlay">
              <span class="overlay-text">R18コンテンツ</span>
            </div>
          </div>
        </div>
      </router-link>

      <div class="p-container">
        <h3 class="image-title">
          {{
            post.title.length > 9 ? post.title.slice(0, 11) + '…' : post.title
          }}
        </h3>
        <router-link :to="`/posts/${post.id}`">
          <img
            :src="`https://yellowokapi2.sakura.ne.jp/Blog/index${post.p_photo}`"
            :alt="post.p_name"
            class="p-photo"
          />
          <p class="p-name">{{ post.p_name }}</p>
        </router-link>
      </div>
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
  justify-content: flex-start; /* 左寄せ */
}

.image-item {
  width: calc(25% - 10px); /* 4つのときと同じサイズ */
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
  object-position: top left;
  transition: filter 0.3s ease;
}

.blurred {
  filter: blur(8px);
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
  margin-top: 8px;
  text-align: left;
  position: relative;
  left: 5px;
  font-size: 16px;
  color: #333;
  margin-right: auto;
}

.p-container {
  width: 100%;
  height: 80px;
}

.p-photo {
  width: 25px;
  height: 25px;
  position: relative;
  top: -10px;
  margin-left: 5px;
  object-fit: cover;
  border-radius: 50%;
}

.p-name {
  display: inline-block;
  position: relative;
  bottom: 18px;
  left: 6px;
  font-size: 13px;
  color: #333;
}

@media screen and (max-width: 1100px) {
  .image-item {
    width: calc(50% - 10px);
  }
}

@media screen and (max-width: 768px) {
  .image-title {
    font-size: 15px;
  }

  .p-photo {
    width: 20px;
    height: 20px;
    position: relative;
    top: -14px;
    margin-left: 5px;
    object-fit: cover;
    border-radius: 50%;
  }

  .p-name {
    display: inline-block;
    position: relative;
    bottom: 20px;
    left: 6px;
    font-size: 12px;
    color: #333;
  }
}
</style>
