<!-- ArticleCatchPage 画像を表示する部分 -->
<script setup lang="ts">
import { defineProps } from 'vue'
import ImageItem from '../basics/APImageItem.vue'

type Post = {
  id: number
  title: string
  url: string
  R18: number
  p_name: string
  p_photo: string
}

// props を型付きで定義して、props.posts にアクセスできるようにする
const props = defineProps<{ posts: Post[] }>()
</script>

<template>
  <ul class="image-gallery">
    <ImageItem v-for="post in props.posts" :key="post.id" :post="post" />
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
</style>
