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
  <div class="search-page">
    <header class="search-header">
      <SearchField />
    </header>

    <main class="search-results">
      <div v-if="posts.length > 0">
        <SectionTitle
          :title="`${tagsName} の検索結果`"
          :subtitle="`${posts.length}件見つかりました`"
        />
        <ImageGallery :posts="posts" />
      </div>
      <div v-else class="no-results">
        <p>該当する検索結果はありません。</p>
        <span>別のキーワードで試してみてください。</span>
      </div>
    </main>
  </div>
</template>

<style scoped>
.search-page {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.search-header {
  display: flex;
  justify-content: center;
  margin-bottom: 2.5rem;
}

.search-results {
  width: 100%;
}

.no-results {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 4rem 1rem;
  color: #666;
  background-color: #f9f9f9;
  border-radius: 8px;
}

.no-results p {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0 0 0.5rem;
}

.no-results span {
  font-size: 1rem;
}

@media (min-width: 768px) {
  .search-page {
    padding: 3rem 2rem;
  }
}
</style>
