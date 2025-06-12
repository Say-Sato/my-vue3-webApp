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
    <!-- コンポーネントに分離！ -->
    <ImageList :images="post?.images ?? []" />

    <h1 class="title">{{ post?.title }}</h1>
    <div class="dtl">{{ post?.body || '本文が入っていません' }}</div>
    <ArticleTags :tagsMsg="post?.tags ?? []" />
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

.favorite {
  position: relative;
  display: block;
  text-align: right;
  margin: 12px 40px 0 0;
}
</style>
