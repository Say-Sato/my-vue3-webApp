<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import ImageGallery from '../components/APImageGallery.vue'
import { useUserStore } from '@/stores/user'

export type PostResponse = {
  id: number
  p_id: string
  title: string
  tags: number[]
  url: string
  body: string
  R18: number
  s_url: string
  p_name: string
  p_photo: string
}

const posts = ref<PostResponse[]>([])
const userStore = useUserStore()
const id = ref(userStore.id)
const loginName = ref(userStore.name)

const fetchData = async () => {
  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/MyPageCatchAPI.php/${id.value}`,
    )
    posts.value = await response.json()
  } catch (error) {
    console.error('Error fetching data:', error)
  }
}

onMounted(fetchData)

const processedPosts = computed(() =>
  posts.value.map(post => ({
    ...post,
    truncatedTitle:
      post.title.length > 9 ? post.title.slice(0, 9) + '…' : post.title,
  })),
)
</script>

<template>
  <div class="title">
    <h1>{{ loginName }}の投稿一覧</h1>
  </div>
  <ImageGallery :posts="processedPosts" />
</template>

<style scoped>
.title {
  font-weight: 400;
  font-size: 24px;
  font-style: normal;
  border-bottom: 2px solid #777;
  padding-bottom: 10px;
}
</style>
