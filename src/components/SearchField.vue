<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const inputText = ref('') // input用文字列
const keywords = ref<string[]>([]) // 分割後配列

// ページ読み込み時にlocalStorageから値を取得
onMounted(() => {
  const saved = localStorage.getItem('searchInput')
  if (saved) inputText.value = saved
})

// inputTextの変更を監視してlocalStorageに保存
watch(inputText, newVal => {
  localStorage.setItem('searchInput', newVal)
})

const submitSearch = () => {
  if (!inputText.value.trim()) return

  // 空白で分割して余計な空白を削除、重複はSetで排除
  const words = inputText.value
    .split(/\s+/)
    .map(word => word.trim())
    .filter(word => word.length > 0)

  // 重複を排除
  keywords.value = Array.from(new Set(words))
  const strSpace: string = keywords.value.join(' ')
  inputText.value = strSpace

  router.push({
    path: '/search',
    query: { words: keywords.value.join(',') },
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
