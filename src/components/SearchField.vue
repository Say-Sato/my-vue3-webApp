<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
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

// アンマウント時にlocalStorageを削除
onBeforeUnmount(() => {
  localStorage.removeItem('searchInput')
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
  <form class="search-field" @submit.prevent="submitSearch">
    <input
      v-model="inputText"
      type="search"
      placeholder="キーワードで検索"
      class="search-input"
    />
    <button type="submit" class="search-button">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="icon"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
      </svg>
      <span>検索</span>
    </button>
  </form>
</template>

<style scoped>
.search-field {
  display: flex;
  width: 100%;
  max-width: 600px;
  border-radius: 9999px;
  background-color: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: box-shadow 0.2s ease-in-out;
}

.search-field:focus-within {
  box-shadow: 0 4px 16px rgba(0, 123, 255, 0.2);
}

.search-input {
  flex-grow: 1;
  padding: 12px 20px;
  border: none;
  background-color: transparent;
  font-size: 16px;
  outline: none;
  color: #333;
}

.search-input::placeholder {
  color: #999;
}

.search-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 24px;
  border: none;
  background-color: #007bff;
  color: white;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s ease-in-out;
}

.search-button:hover {
  background-color: #0056b3;
}

.icon {
  width: 18px;
  height: 18px;
}
</style>
