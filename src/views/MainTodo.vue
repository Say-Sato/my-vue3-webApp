<script setup lang="ts">
import { onMounted, ref } from 'vue'
import ImageGallery from '../components/MTPostList.vue'

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

const posts = ref<PostResponse[]>([])
const topFourPosts = ref<PostResponse[]>([])

const fetchData = async () => {
  try {
    const response = await fetch(
      'https://yellowokapi2.sakura.ne.jp/Vue/api/BlogAllCatchAPI.php',
    )
    const data = (await response.json()) as PostResponse[]
    posts.value = data

    topFourPosts.value = posts.value.slice(0, 4)
  } catch (error) {
    console.error('Error fetching data:', error)
  }
}
onMounted(fetchData)

// 表示する投稿（最大4件に制限）
</script>

<template>
  <div class="title">
    <h1>新着一覧</h1>
  </div>
  <ImageGallery :posts="topFourPosts" />
</template>

<style scoped>
h1 {
  font-weight: 400;
  font-size: 24px;
  font-style: normal;
  border-bottom: 2px solid #777;
  padding-bottom: 10px;
}
</style>
