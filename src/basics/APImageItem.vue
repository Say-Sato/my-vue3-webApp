<!-- ArticleCatchPage 画像を表示する部分 -->
<script setup lang="ts">
import { defineProps } from 'vue'

type Post = {
  illust_id: number
  illust_title: string
  illust_R18: number
  profile_name: string
  profile_photo: string
  image_url: string
}

const props = defineProps({
  post: {
    type: Object as () => Post,
    required: true,
  },
})
</script>

<template>
  <li :key="props.post.illust_id" class="image-item">
    <router-link :to="`/posts/edit/${props.post.illust_id}`">
      <div class="box3">
        <div class="image-wrapper">
          <img
            :src="props.post.image_url"
            :alt="props.post.illust_title"
            class="image"
          />
          <div v-if="props.post.illust_R18" class="blur-overlay">R18</div>
        </div>
      </div>
    </router-link>
  </li>
</template>

<style scoped>
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

@media screen and (max-width: 1100px) {
  .image-item {
    width: calc(50% - 10px);
  }
}
</style>
