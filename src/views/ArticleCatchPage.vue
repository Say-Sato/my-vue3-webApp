<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
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
      <button class="disabled-button">通知オフ</button>
      <button class="follow-button">フォロー</button>
      <button class="icon-button">🔁</button>
    </div>
  </div>

  <div class="title">
    <h1>投稿一覧</h1>
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

.disabled-button {
  background-color: #eee;
  border: none;
  color: #666;
  padding: 4px 8px;
  border-radius: 6px;
  cursor: not-allowed;
}

.follow-button {
  background-color: #ddd;
  border: none;
  padding: 4px 8px;
  border-radius: 6px;
  cursor: pointer;
}

.icon-button {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}
</style>
