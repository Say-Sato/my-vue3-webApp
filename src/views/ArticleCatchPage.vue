<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import IconButton from '@/basics/IconButton.vue'
import LinkCopy from '@/components/LinkCopy.vue'
import SectionTitle from '@/basics/SectionTitle.vue'
import ImageGallery from '../components/APImageGallery.vue'
import { useUserStore } from '@/stores/user'

export type PostResponse = {
  illust_id: number
  illust_title: string
  illust_R18: number
  profile_name: string
  profile_photo: string
  image_url: string
  illust_profile_id: number
  illust_body: string
  illust_s_url: string
  profile_login_id: string
}

const posts = ref<PostResponse[]>([])
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
    fullProfilePhoto: `http://yellowokapi2.sakura.ne.jp/Blog/index${post.profile_photo}`,
  })),
)
</script>

<template>
  <div class="profile-container" v-if="posts.length > 0">
    <img
      :src="processedPosts[0].fullProfilePhoto"
      alt="プロフィール画像"
      class="profile-photo"
    />
    <div class="profile-info">
      <h2>{{ posts[0].profile_name }}</h2>
      <p>ID: {{ posts[0].profile_login_id }}</p>
      <p>mailaddress@example.com</p>
      <!-- ← メールはpostsにないので仮置き -->
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
    </div>
  </div>

  <!-- 投稿一覧 -->
  <SectionTitle title="投稿一覧" />

  <ImageGallery :posts="processedPosts" />
</template>

<style scoped>
.profile-container {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 0;
  border-bottom: 1px solid #ccc;
}

.profile-photo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-info {
  flex: 1;
}

.profile-info h2 {
  margin: 0;
  font-size: 20px;
}

.profile-info p {
  margin: 4px 0;
  color: #555;
}

.profile-actions {
  display: flex;
  flex-direction: row;
  gap: 8px;
}
</style>
