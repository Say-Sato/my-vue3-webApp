<script setup lang="ts">
import { onMounted, ref } from 'vue'
import ImageGallery from '../components/FVImageGallery.vue'
import SectionTitle from '@/basics/SectionTitle.vue'
import SearchField from '../components/SearchField.vue'

export type PostResponse = {
  id: number
  p_id: number
  title: string
  url: string
  body: string
  R18: number
  public: number
  s_url: string
  p_name: string
  p_photo: string
  showProfile: boolean
  tags: number[]
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
    posts.value = data.map((post: PostResponse) => ({
      ...post,
      showProfile: true,
    }))

    // 最初の要素の tags を格納
    if (data.length > 0 && data[0].tags) {
      tagsName.value = String(data[0].tags)
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
      <SectionTitle
        :title="tagsName + 'の検索結果：' + '●●件' || 'error:タグ読み取り失敗'"
      />
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
