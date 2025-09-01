<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import ImageGallery from '../components/FVImageGallery.vue'
import SectionTitle from '@/basics/SectionTitle.vue'
import PostProfile from '@/components/PostProfile.vue'
import type { Favorite } from '@/types/PostResponse'

const route = useRoute()
const posts = ref<Favorite[]>([])
const id = ref(route.params.id)

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
  <ImageGallery :posts="posts" />
</template>

<style scoped></style>
