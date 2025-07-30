<script lang="ts" setup>
import { defineProps } from 'vue'

// 親コンポーネントからisOpenを受け取る
const props = defineProps<{
  isOpen: boolean
}>()
</script>

<template>
  <div
    class="icon"
    :class="{ open: props.isOpen }"
    @click.stop="$emit('toggle-menu')"
  >
    <span></span>
    <span></span>
    <span></span>
  </div>
</template>

<style scoped>
.icon {
  display: inline-block;
  width: 30px; /* ハンバーガーメニューの幅 */
  height: 30px; /* ハンバーガーメニューの高さ */
  position: relative;
  top: 10px;
  transition: transform 0.3s ease; /* アニメーションの追加 */
}

.icon span {
  display: block;
  position: absolute;
  height: 2px; /* 線の太さ */
  width: 100%;
  background-color: #333; /* 線の色 */
  transition: all 0.3s ease; /* アニメーションの追加 */
}

.icon span:nth-child(1) {
  top: 0;
}

.icon span:nth-child(2) {
  top: 10px; /* 中央線 */
}

.icon span:nth-child(3) {
  top: 20px;
}

.icon.open span:nth-child(1) {
  transform: rotate(45deg);
  top: 10px; /* 中央に移動 */
}

.icon.open span:nth-child(2) {
  opacity: 0; /* 中央線を消す */
}

.icon.open span:nth-child(3) {
  transform: rotate(-45deg);
  top: 10px; /* 中央に移動 */
}
</style>
