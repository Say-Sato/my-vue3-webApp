<script setup lang="ts">
import { onMounted, ref, watch, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'

import ArticleTags from '@/basics/ArticleTags.vue'
import Favorite from '@/components/FavoriteButton.vue'
import ImageList from '@/components/ArticleImageList.vue'
import PrevNextButtons from '@/components/PrevNextButtons.vue'
import Profile from '@/components/UserProfile.vue'
import Comment from '@/components/CommentSection.vue'
import type { Image, PostGallery } from '@/types/PostResponse'

const route = useRoute()
const post = ref<PostGallery>()

const fetchData = async () => {
  const id = ref(route.params.id)
  const response = await fetch(
    `https://yellowokapi2.sakura.ne.jp/Vue/api/BlogAllCatchAPI.php/${id.value}`,
  )
  const data: PostGallery = await response.json()
  data.images.sort((a: Image, b: Image) => a.sort_order - b.sort_order)
  post.value = data
}

onMounted(fetchData)

watch(
  () => route.params.id,
  () => {
    fetchData()
  },
)

// ✅ 画面幅が800px以上かを監視
const isWideScreen = ref(window.innerWidth > 800)
const updateScreenSize = () => {
  isWideScreen.value = window.innerWidth > 800
}

onMounted(() => {
  window.addEventListener('resize', updateScreenSize)
})
onBeforeUnmount(() => {
  window.removeEventListener('resize', updateScreenSize)
})
</script>

<template>
  <div class="main-layout">
    <!-- メインエリア -->
    <div class="container main">
      <ImageList :images="post?.images ?? []" />

      <div class="title-favorite-wrapper">
        <h1 class="title">{{ post?.illust_title }}</h1>
        <span class="favorite">
          <Favorite :i_id="post?.illust_id ?? 0" />
        </span>
      </div>

      <div class="dtl">
        {{ post?.illust_body || '本文が入っていません' }}
      </div>
      <ArticleTags :tagsMsg="post?.tags ?? []" />

      <div v-if="post" class="prev-next-wrapper">
        <PrevNextButtons
          :prevId="post?.prev_id ?? 0"
          :nextId="post?.next_id ?? 0"
        />
      </div>
    </div>

    <!-- サイドエリア（画面幅に応じて表示切り替え） -->
    <div v-if="isWideScreen" class="sidebar">
      <Profile
        :name="post?.profile_name ?? 'deleted user'"
        :p_photo="post?.profile_photo"
        :profile_login_id="post?.profile_login_id"
      />
      <div class="sidebar-divider"></div>
      <Comment :post_id="post?.illust_id || 0" />
    </div>

    <div v-else class="sidebar-mobile">
      <Profile
        :name="post?.profile_name ?? 'deleted user'"
        :p_photo="post?.profile_photo"
        :profile_id="post?.profile_login_id"
      />
      <div class="sidebar-divider"></div>
      <Comment :post_id="post?.illust_id || 0" />
    </div>
  </div>
</template>

<style scoped>
.main-layout {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: flex-start;
  width: 100%;
  box-sizing: border-box;
}

/* メインエリア */
.container.main {
  width: calc(100% - 300px);
  padding: 20px;
  margin-left: -70px;
  margin-right: 20px;
  border-right: 2px dashed rgba(0, 0, 0, 0.2);
  box-sizing: border-box;
}

/* サイドバー */
.sidebar {
  width: 250px;
  padding: 20px;
  box-sizing: border-box;
}

/* タイトルとお気に入りを縦に並べる */

.title {
  text-align: center;
  margin: 0 0 8px 0;
  font-size: 20px;
  font-weight: bold;
  position: relative;
  left: 0;
}

.favorite {
  display: flex;
  justify-content: flex-end;
  margin: 18px 0 32px 0; /* 上下の間隔 */
}

/* 本文 */
.dtl {
  line-height: 1.5;
  margin-bottom: 16px;
  position: relative;
}

/* 本文下のグレー線 */
.dtl::after {
  content: '';
  display: block;
  width: 100%;
  height: 1px;
  background-color: #ccc;
  margin: 24px 0 10px;
}

.image-container {
  position: relative;
  margin-top: 10px;
  display: flex;
  justify-content: center; /* ← h1 を中央揃え */
  align-items: center; /* ← 高さを揃える */
}

.title {
  font-size: 20px;
  font-weight: bold;
  margin: 0;
}

.prev-next-wrapper {
  width: 100%;
  max-width: 960px;
  margin: 0 auto;
  box-sizing: border-box;
  padding: 50px 20px 0;
}

.follow-btn {
  background-color: white;
  border: 1px solid #aaa;
  border-radius: 12px;
  padding: 6px 12px;
  font-size: 14px;
  cursor: pointer;
}

.sidebar-divider {
  width: 280px;
  height: 1px;
  background-color: #ccc;
  margin: 30px 0 10px;
}

/* タブレットサイズ以下でタイトルを画像下に表示 */
@media screen and (max-width: 800px) {
  .main-layout {
    flex-direction: column;
    align-items: center; /* ← 中央揃えにするため追加 */
  }

  .container.main {
    width: 100%; /* ← 上書き */
    max-width: 800px; /* ← 任意で制限 */
    padding: 20px 0 0 0; /* ← paddingリセット */
    margin: 0 auto; /* ← 左右中央 */
    border-right: none; /* ← 線を消す */
  }

  .sidebar {
    display: none;
  }

  .sidebar-mobile {
    width: 100%;
    padding: 20px 0 0 0;
  }
}

/* スマホサイズ以下でタイトルを画像下に表示 */
@media screen and (max-width: 480px) {
}
</style>
