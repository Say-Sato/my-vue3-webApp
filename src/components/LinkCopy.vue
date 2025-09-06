<script setup lang="ts">
import { ref } from 'vue'
import IconButton from '@/basics/IconButton.vue'

const showPopup = ref(false)

const copyUrl = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
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
  <div class="container">
    <IconButton
      label=""
      icon-class="fa-regular fa-copy"
      backgroundColor="primary"
      textColor="white"
      @click="copyUrl"
    />

    <!-- トースト通知 -->
    <transition name="slide-fade">
      <div v-if="showPopup" class="toast-popup">コピーしました！</div>
    </transition>
  </div>
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
