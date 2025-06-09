<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import ArticleTags from '@/basics/ArticleTags.vue'
import Favorite from '@/components/FavoriteIcon.vue'
import type { PostResponse } from '@/views/ArticleCatchPage.vue'

const route = useRoute()
const id = route.params.id
const post = ref<PostResponse>()

// データを取得する非同期関数
const fetchData = async () => {
  const response = await fetch(
    `https://yellowokapi2.sakura.ne.jp/Vue/api/BlogAllCatchAPI.php/${id}`,
  )

  // APIレスポンスのデータをログに表示
  const data = await response.json()

  // postにデータを格納
  post.value = data
}

onMounted(fetchData)
</script>

<template>
  <div class="container">
    <div class="image-container">
      <img :src="`${post?.url}`" alt="Sample Image" class="image" />
    </div>
    <h1 class="title">{{ post?.title }}</h1>
    <div class="dtl">{{ post?.body || '本文が入っていません' }}</div>
    <ArticleTags :tagsMsg="post?.tags ?? []"></ArticleTags>
    <span class="favorite">
      <Favorite :i_id="post?.id ?? 0" />
    </span>
  </div>
</template>

<style scoped>
.title {
  margin: 12px;
  font-size: 20px;
  font-weight: bold;
  text-align: center;
}

.dtl {
  line-height: 1.5;
}

.image-container {
  position: relative; /* 親要素に相対位置を指定 */
  width: 95%;
  height: 600px;
  display: block;
  overflow: hidden;
  background-color: #f0f0f0;
  margin: 0 auto;
}

.image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  object-position: center;
}

.favorite {
  position: absolute;
  top: 630px;
  right: 40px;
}
</style>
