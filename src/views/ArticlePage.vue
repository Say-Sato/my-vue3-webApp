<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'

import ArticleTags from '@/basics/ArticleTags.vue'
import Favorite from '@/components/FavoriteIcon.vue'
import ImageList from '@/components/ArticleImageList.vue'
import PrevNextButtons from '@/components/PrevNextButtons.vue'
import Profile from '@/components/UserProfile.vue'

export type Image = {
  image_id: number
  image_url: string
  sort_order: number
}

export interface PostResponse {
  id: number
  title: string
  body: string
  tags: number[]
  images: Image[]
  prev_id: number
  next_id: number
  p_name: string
  p_photo: string
}

const route = useRoute()
const post = ref<PostResponse>()

const fetchData = async () => {
  const id = ref(route.params.id)
  const response = await fetch(
    `https://yellowokapi2.sakura.ne.jp/Vue/api/BlogAllCatchAPI.php/${id.value}`,
  )
  const data: PostResponse = await response.json()

  // sort_orderでソートして格納
  data.images.sort((a: Image, b: Image) => a.sort_order - b.sort_order)
  post.value = data
}

onMounted(fetchData)

// IDの変化を監視して再取得
watch(
  () => route.params.id,
  () => {
    fetchData()
  },
)
</script>

<template>
  <div class="main-layout">
    <!-- メインエリア -->
    <div class="container main">
      <ImageList :images="post?.images ?? []" />

      <div class="title-favorite-wrapper">
        <h1 class="title">{{ post?.title }}</h1>
        <span class="favorite">
          <Favorite :i_id="post?.id ?? 0" />
        </span>
      </div>

      <div class="dtl">{{ post?.body || '本文が入っていません' }}</div>
      <ArticleTags :tagsMsg="post?.tags ?? []" />

      <div v-if="post" class="prev-next-wrapper">
        <PrevNextButtons
          :prevId="post?.prev_id ?? 0"
          :nextId="post?.next_id ?? 0"
        />
      </div>
    </div>

    <!-- サイドエリア -->
    <div class="sidebar">
      <Profile
        :name="post?.p_name ?? 'deleted user'"
        :p_photo="post?.p_photo"
      />
    </div>
  </div>
</template>

<style scoped>
.main-layout {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

/* メインエリア */
.container.main {
  width: calc(100% - 300px);
  padding: 20px;
  border-right: 2px dashed rgba(0, 0, 0, 0.2);
  box-sizing: border-box;
}

/* サイドバーエリア */
.sidebar {
  width: 300px;
  height: auto;
  padding: 20px;
  box-sizing: border-box;
}

.title-favorite-wrapper {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 12px 0;
  position: relative; /* これで絶対配置の基準にしてもいい */
}

.title {
  flex-grow: 1; /* 余った幅を全部使う */
  text-align: center; /* テキストを中央に */
  margin: 0;
  font-size: 20px;
  font-weight: bold;
  position: relative; /* z-indexが必要なら */
  left: 80px; /* 位置調整したければ */
}

.favorite {
  display: block;
  margin: 0 40px 0 0;
  z-index: 1; /* タイトルの上に表示したければ */
}

.dtl {
  line-height: 1.5;
}

.image-container {
  position: relative;
  width: 95%;
  height: 600px;
  display: block;
  overflow: hidden;
  background-color: #f0f0f0;
  margin: 0 auto 24px;
}

.image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  object-position: center;
}

.prev-next-wrapper {
  width: 100%;
  max-width: 960px;
  margin: 0 auto;
  box-sizing: border-box;
  padding: 0 20px;
}

/* タブレットサイズ以下でタイトルを画像下に表示 */
@media screen and (max-width: 800px) {
  .main-layout {
    flex-direction: column;
  }

  .container.main {
    width: 100%;
    border-right: none;
  }

  .sidebar {
    display: none;
  }
}

/* スマホサイズ以下でタイトルを画像下に表示 */
@media screen and (max-width: 480px) {
}
</style>
