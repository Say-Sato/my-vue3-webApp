<script setup lang="ts">
defineProps({
  label: {
    type: String,
    required: true,
    default: 'Search', // デフォルトのテキスト
  },
  color: {
    type: String,
    required: false,
    default: 'white', // アイコンの色を黒に変更
  },
  iconClass: {
    type: String,
    required: false,
    default: 'fa-solid fa-magnifying-glass', // デフォルトのアイコン
  },
  backgroundColor: {
    type: String,
    required: false,
    default: '#1e1e1e', // ボタンのデフォルト背景色
  },
})
</script>

<template>
  <button
    class="icon-button"
    :style="{ backgroundColor: backgroundColor }"
    v-bind="$attrs"
  >
    <i :class="iconClass" :style="{ color: color }"></i>
    {{ label }}
  </button>
</template>

<style scoped>
.icon-button {
  color: #ffffff;
  border: none;
  border-radius: 15px; /* 角を丸める */
  padding: 8px 16px; /* 内側の余白を設定 */
  font-size: 14px; /* フォントサイズ */
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* ほんのり影をつける */
  transition:
    background-color 0.3s ease,
    box-shadow 0.3s ease; /* 背景色と影のアニメーション */
}

.icon-button:active {
  background-color: #d0d0d0 !important; /* ホバー時に背景色が変わる */
  box-shadow: 0 2px 6px rgba(229, 52, 140, 0.6); /* ボタンを押した時の影 */
  transform: translateY(2px); /* ボタンが押し込まれるように見せる */
}
</style>
