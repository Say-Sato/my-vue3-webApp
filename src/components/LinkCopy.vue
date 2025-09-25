<script setup lang="ts">
import { ref } from 'vue'
import { useUserStore } from '@/stores/user'
import IconButton from '@/basics/IconButton.vue'

const showPopup = ref(false)
const userStore = useUserStore()

// コピー先のベースURL（ここを書き換える）
const baseUrl = 'https://yellowokapi2.sakura.ne.jp/user-profile/'
const id = ref('')

const copyUrl = async () => {
  try {
    // 現在のURLの末尾の数字を正規表現で取得
    const match = window.location.href.match(/(\d+)$/)

    id.value = match?.[1] ?? userStore.id ?? '0'

    const newUrl = `${baseUrl}${id.value}`

    await navigator.clipboard.writeText(newUrl)

    showPopup.value = true
    setTimeout(() => {
      showPopup.value = false
    }, 2000) // 2秒で消える
  } catch (err) {
    console.error('コピーに失敗しました: ', err)
  }
}
</script>

<template>
  <IconButton
    label=""
    iconClass="fa-regular fa-copy"
    backgroundColor="primary"
    textColor="white"
    @click="copyUrl"
  />

  <!-- トースト通知 -->
  <transition name="slide-fade">
    <div v-if="showPopup" class="toast-popup">コピーしました！</div>
  </transition>
</template>

<style scoped>
.toast-popup {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #000;
  color: #fff;
  font-size: 14px;
  padding: 8px 16px;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
  z-index: 9999;
}

/* フェード＋スライドアニメーション */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.4s ease;
}
.slide-fade-enter-from {
  opacity: 0;
  transform: translateY(20px);
}
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
