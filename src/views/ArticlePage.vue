<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import ArticleTags from '@/basics/ArticleTags.vue'
import Favorite from '@/components/FavoriteIcon.vue'
import ImageList from '@/components/ArticleImageList.vue'

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
}

const route = useRoute()
const id = route.params.id
const post = ref<PostResponse>()

const fetchData = async () => {
  const response = await fetch(
    `https://yellowokapi2.sakura.ne.jp/Vue/api/BlogAllCatchAPI.php/${id}`,
  )
  const data = await response.json()

  // sort_orderでソートして格納
  data.images.sort((a: Image, b: Image) => a.sort_order - b.sort_order)
  post.value = data
}

onMounted(fetchData)
</script>

<template>
  <div class="container">
    <ImageList :images="post?.images ?? []" />

    <div class="title-row">
      <h1 class="title">{{ post?.title }}</h1>
      <span class="favorite">
        <Favorite :i_id="post?.id ?? 0" />
      </span>
    </div>

    <div class="dtl">{{ post?.body || '本文が入っていません' }}</div>
    <ArticleTags :tagsMsg="post?.tags ?? []" />
  </div>
</template>

<style scoped>
.title-row {
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

.favorite {
  position: absolute;
  right: 40px;
}
</style>
