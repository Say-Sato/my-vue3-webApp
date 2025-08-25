<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const inputText = ref('') // input用は文字列
const keywords = ref<string[]>([]) // 分割後の配列

const submitSearch = () => {
  if (!inputText.value.trim()) return

  // 空白で分割して余計な空白を削除
  keywords.value = inputText.value
    .split(/\s+/)
    .map(word => word.trim())
    .filter(word => word.length > 0)

  // クエリ文字列に結合して URL に渡す
  const queryParam = encodeURIComponent(keywords.value.join(' '))

  router.push({
    name: 'SearchResult',
    query: { q: queryParam },
  })
}
</script>

<template>
  <div class="search-bar">
    <input
      v-model="inputText"
      placeholder="検索ワード"
      class="search-input"
      @keyup.enter="submitSearch"
    />
    <button @click="submitSearch" class="search-button">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="icon"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"
        />
      </svg>
    </button>
  </div>
  <!-- デバッグ用: 分割後の配列表示 -->
  {{ keywords }}
</template>

<style scoped>
.search-bar {
  display: flex;
  align-items: center;
  width: 100%;
  max-width: 400px;
  border: 1px solid #ccc;
  border-radius: 9999px;
  overflow: hidden;
}

.search-input {
  flex: 1;
  padding: 8px 12px;
  border: none;
  font-size: 14px;
  outline: none;
}

.search-input:focus {
  outline: none;
}

.search-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 100%;
  border: none;
  border-left: 1px solid #ccc;
  background: none;
  cursor: pointer;
  color: #666;
}

.search-button:hover {
  color: #111;
}

.icon {
  width: 16px;
  height: 16px;
}
</style>
