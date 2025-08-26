<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import ImageGallery from '../components/FVImageGallery.vue'
import SectionTitle from '@/basics/SectionTitle.vue'
import SearchField from '../components/SearchField.vue'
import type { Favorite } from '@/types/PostResponse'

const route = useRoute()

const tag = ref('')
const words = ref('')
const posts = ref<Favorite[]>([])
const tagsName = ref('')

// ---- fetchDataを tag用 / words用 に分割 ----
const fetchByTag = async (queryTag: string) => {
  tag.value = decodeURIComponent(queryTag)

  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/TagCatchAPI.php?tag=${tag.value}`,
    )
    const data = await response.json()
    console.log('APIレスポンス(tag):', data)

    if (Array.isArray(data)) {
      posts.value = data.map((post: Favorite) => ({
        ...post,
        showProfile: true,
      }))
    } else {
      console.error('APIから配列が返っていません:', data)
      posts.value = []
    }

    if (data.length > 0 && data[0].tags) {
      tagsName.value = String(data[0].tags)
    }
  } catch (error) {
    console.error('Error fetching data (tag):', error)
  }
}

const fetchByWords = async (queryWords: string) => {
  words.value = decodeURIComponent(queryWords)

  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/Illust_tagsSearchAPI.php?words=${words.value}`,
    )
    const data = await response.json()
    //console.log('APIレスポンス(words):', data)

    if (Array.isArray(data)) {
      posts.value = data.map((post: Favorite) => ({
        ...post,
        showProfile: true,
      }))
    } else {
      console.error('APIから配列が返っていません:', data)
      posts.value = []
    }

    if (data.length > 0) {
      tagsName.value = words.value
    }
  } catch (error) {
    console.error('Error fetching data (words):', error)
  }
}

// ---- watchでクエリを監視して呼び分け ----
watch(
  () => route.query,
  newQuery => {
    const queryTag = (newQuery.tag as string) || ''
    const queryWords = (newQuery.words as string) || ''

    if (queryTag.trim() && !queryWords.trim()) {
      fetchByTag(queryTag)
    } else if (queryWords.trim() && !queryTag.trim()) {
      fetchByWords(queryWords)
    } else {
      console.error('タグかワードのどちらか一方を指定してください')
    }
  },
  { deep: true, immediate: true }, // 初回マウント時も実行
)
</script>

<template>
  <div class="container">
    <SearchField />
  </div>
  <div v-if="posts.length > 0">
    <!-- タグの表示 -->
    <div class="post-item" v-if="posts.length > 0">
      <SectionTitle
        :title="
          tagsName + 'の検索結果：' + `${posts.length}件` ||
          'error:タグ読み取り失敗'
        "
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
