<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import ImageGallery from '../components/FVImageGallery.vue'

// 元APIのレスポンス型
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

const route = useRoute()
const posts = ref<PostResponse[]>([])
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

// 新しいImageGalleryのPost型に変換
const processedPosts = computed(() =>
  posts.value.map(post => ({
    id: post.illust_id,
    p_id: post.illust_profile_id,
    title: post.illust_title,
    url: post.image_url,
    body: post.illust_body,
    R18: post.illust_R18,
    public: 1, // APIに無いので仮で公開状態を1に
    s_url: post.illust_s_url,
    p_name: post.profile_name,
    p_photo: post.profile_photo,
    showProfile: false, // 必要に応じて条件分岐
  })),
)
</script>

<template>
  <div class="profile-container" v-if="posts.length > 0">
    <img
      :src="`http://yellowokapi2.sakura.ne.jp/Blog/index${posts[0].profile_photo}`"
      alt="プロフィール画像"
      class="profile-photo"
    />
    <div class="profile-info">
      <h2>{{ posts[0].profile_name }}</h2>
      <p>ID: {{ posts[0].profile_login_id }}</p>
      <p>mailaddress@example.com</p>
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
