<script setup lang="ts">
import { onMounted, ref } from 'vue'
import ImageGallery from '../components/MTPostList.vue'
import SearchField from '../components/SearchField.vue'

export type PostResponse = {
  id: number
  p_id: string
  title: string
  tag: string
  url: string
  body: string
  R18: number
  s_url: string
  p_name: string
  p_photo: string
}

// タグを保持する変数
const tag = ref('')
const posts = ref<PostResponse[]>([])
const tagsName = ref('')

const fetchData = async () => {
  // URLから検索ワード、タグを取得
  const pathParts = window.location.pathname.split('/')
  tag.value = decodeURIComponent(pathParts[pathParts.length - 1])

  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/TagCatchAPI.php/${tag.value}`,
    )

    const data = await response.json()

    console.log('APIレスポンス:', data)

    posts.value = data // posts 配列にデータを格納

    // tags（または適切なキー）をtagsName.valueに格納
    if (data.tag) {
      tagsName.value = data.tag // ここでAPIから返されるtagsを格納
    }

    console.log('格納されたタグ:', tagsName.value)
  } catch (error) {
    console.error('Error fetching data:', error)
  }
}

// APIからデータを取得
onMounted(fetchData)
</script>

<template>
  <div class="container">
    <SearchField />
  </div>
  <div v-if="posts.length > 0">
    <!-- タグの表示 -->
    <div class="post-item" v-if="posts.length > 0">
      <h2>タグ: {{ posts[0].tag || 'error:タグ読み取り失敗' }} の検索結果</h2>
    </div>

    <!-- 投稿データの表示 -->
    <ImageGallery :posts="posts" />
    <!-- posts 配列を渡す -->
  </div>
  <!-- データがなかった場合 -->
  <div v-else>
    <p>該当する検索結果はありません。</p>
  </div>
</template>

<style scoped>
.title {
  font-weight: 400;
  font-size: 24px;
  font-style: normal;
  border-bottom: 2px solid #777;
  padding-bottom: 10px;
}

.container {
  display: flex;
  padding-top: 10px;
}
</style>
