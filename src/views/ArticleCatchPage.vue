<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
//import IconButton from '@/basics/IconButton.vue'
//import LinkCopy from '@/components/LinkCopy.vue'
import SectionTitle from '@/basics/SectionTitle.vue'
import ImageGallery from '../components/APImageGallery.vue'
import PostProfile from '@/components/PostProfile.vue'
import { useUserStore } from '@/stores/user'
import type { Postvalidation } from '@/types/PostResponse'

const posts = ref<Postvalidation[]>([])
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

const processedPosts = computed(() =>
  posts.value.map(post => ({
    ...post,
    truncatedTitle:
      post.illust_title.length > 9
        ? post.illust_title.slice(0, 9) + '…'
        : post.illust_title,
  })),
)
</script>

<template>
  <!--<img
      :src="processedPosts[0].fullProfilePhoto"
      alt="プロフィール画像"
      class="profile-photo"
    />
    <div class="profile-info">
      <h2>{{ posts[0].profile_name }}</h2>
      <p>ID: {{ posts[0].profile_login_id }}</p>
      <p>mailaddress@example.com</p>
    </div>
    <div class="profile-actions">
      <IconButton
        label="通知オフ"
        icon-class="fa-solid fa-bell-slash"
        background-color="#bcbcbc"
        textColor="white"
      />

      <IconButton
        label="フォロー"
        icon-class="fa-solid fa-user-plus"
        background-color="#bcbcbc"
        textColor="white"
      />

      <LinkCopy />
    </div>-->
  <PostProfile
    v-if="posts.length > 0"
    :profile_photo="posts[0].profile_photo"
    :profile_name="posts[0].profile_name"
    :profile_login_id="posts[0].profile_login_id"
  />
  <!-- 投稿一覧 -->
  <SectionTitle title="投稿一覧" />

  <ImageGallery :posts="processedPosts" />
</template>

<style scoped></style>
