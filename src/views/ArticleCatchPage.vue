<script setup lang="ts">
import { onMounted, ref } from 'vue'
import SectionTitle from '@/basics/SectionTitle.vue'
import ImageGallery from '@/basics/ImageGallery.vue'
import PostProfile from '@/components/PostProfile.vue'
import { useUserStore } from '@/stores/user'
import type { MyPage } from '@/types/PostResponse'

const posts = ref<MyPage[]>([])
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
  <div v-if="posts.length > 0">
    <PostProfile
      :profile_photo="posts[0].profile_photo"
      :profile_name="posts[0].profile_name"
      :profile_id="posts[0].profile_id"
      :profile_login_id="posts[0].profile_login_id"
      :only_profile="posts[0].only_profile"
    />
    <SectionTitle title="投稿一覧" />
    <ImageGallery
      v-if="!posts[0].only_profile"
      :posts="posts"
      :showLabel="true"
    />

    <!-- 投稿がないとき -->
    <p v-else class="no-posts">まだ投稿はありません</p>
  </div>
</template>
<style scoped>
.no-posts {
  text-align: center;
  color: #666;
  font-size: 14px;
  margin: 20px 0;
}
</style>
