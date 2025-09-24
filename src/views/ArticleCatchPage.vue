<script setup lang="ts">
import { onMounted, ref } from 'vue'
import SectionTitle from '@/basics/SectionTitle.vue'
import ImageGallery from '@/basics/ImageGallery.vue'
import PostProfile from '@/components/PostProfile.vue'
import { useUserStore } from '@/stores/user'
import type { Favorite } from '@/types/PostResponse'

const posts = ref<Favorite[]>([])
const userStore = useUserStore()
const id = ref(userStore.id)

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
</script>

<template>
  <PostProfile
    v-if="posts.length > 0"
    :profile_photo="posts[0].profile_photo"
    :profile_name="posts[0].profile_name"
    :profile_login_id="posts[0].profile_login_id"
  />
  <SectionTitle title="投稿一覧" />
  <ImageGallery :posts="posts" :showLabel="true" />
</template>
