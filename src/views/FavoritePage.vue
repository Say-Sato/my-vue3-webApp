<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useUserStore } from '@/stores/user'

import ImageGallery from '../components/FVImageGallery.vue'

export type PostResponse = {
  id: number
  p_id: number
  title: string
  url: string
  body: string
  public: number
  R18: number
  s_url: string
  p_name: string
  p_photo: string
  showProfile: boolean
}

const userStore = useUserStore()
const posts = ref<PostResponse[]>([])
const id = ref(userStore.id ?? 0) // nullやundefined回避

const fetchData = async () => {
  try {
    // 1回目のAPI（i_id配列を取得）
    const res1 = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/FavoriteGetAPI.php/${id.value}`,
    )
    if (!res1.ok)
      throw new Error(`Failed to fetch Favorite IDs: ${res1.status}`)
    const json1 = (await res1.json()) as { i_id: number[] }

    if (!json1.i_id || json1.i_id.length === 0) {
      posts.value = []
      return
    }

    // クエリパラメータ作成（例: ?ids=81,78,67,...）
    const idsParam = json1.i_id.join(',')

    // 2回目のAPI呼び出し
    const res2 = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/FavoritePageGetAPI.php?ids=${idsParam}`,
    )
    if (!res2.ok) throw new Error(`Failed to fetch Posts: ${res2.status}`)
    const json2 = (await res2.json()) as PostResponse[]

    posts.value = json2

    posts.value = json2.map(post => ({
      ...post,
      showProfile: true, // 条件はお好みで
    }))
  } catch (error) {
    console.error('Error fetching data:', error)
  }
}

onMounted(fetchData)
</script>

<template>
  <div>
    <ImageGallery :posts="posts" />
  </div>
</template>

<style scoped></style>
