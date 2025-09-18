<script setup lang="ts">
import { onMounted, ref } from 'vue'
import ImageList from '../components/MTPostList.vue'
import type { PostGallery } from '@/types/PostResponse'

const posts = ref<PostGallery[]>([])
const topFourPosts = ref<PostGallery[]>([])

const fetchData = async () => {
  try {
    const response = await fetch(
      'https://yellowokapi2.sakura.ne.jp/Vue/api/BlogAllCatchAPI.php',
    )
    const data = (await response.json()) as PostGallery[]
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
  <ImageList :posts="topFourPosts" />
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
