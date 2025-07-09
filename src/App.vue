<script lang="ts">
//import MainTodo from '@/components/MainTodo.vue'
//router-viewでMainTodo.vueを呼び出している
import TheHeader from '@/containers/TheHeader.vue' // ヘッダー
import TheFooter from '@/basics/TheFooter.vue' // フッター
//import UserProfile from '@/components/UserProfile.vue' // フッター

import { computed } from 'vue'
import { useRoute } from 'vue-router'

export default {
  components: {
    TheHeader,
    TheFooter,
    //UserProfile,
  },
  data() {
    const route = useRoute()

    // 現在のルートroute.meta.showHeaderを取得
    const showProfile = computed(() => route.meta.showProfile !== false)
    const showHeader = computed(() => route.meta.showHeader !== false)

    return {
      showProfile,
      showHeader,
    }
  },
}
</script>

<template>
  <head>
    <link
      href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&display=swap"
      rel="stylesheet"
    />
  </head>
  <div class="container">
    <TheHeader v-if="showHeader" />
    <main class="main" :class="{ 'no-border': !showProfile }">
      <router-view />
    </main>
    <div class="br"></div>
    <TheFooter />
    <!--<UserProfile v-if="showProfile" />-->
  </div>
</template>

<style>
html {
  font-family: 'Yu Gothic', '游ゴシック', 'YuGothic', sans-serif;
}
</style>

<style scoped>
html,
body {
  margin: 0; /* デフォルトのマージンをリセット */
  padding: 0; /* デフォルトのパディングをリセット */
  height: 100%; /* 高さを100%に設定 */
  min-width: 370px;
}

body {
  display: flex;
  flex-direction: column;
  min-height: 100vh; /* 最小高さを100vhに設定 */
  padding-top: 80px; /* ヘッダーの高さに応じて余白を追加 */
  overflow-y: auto; /* 縦方向にスクロール可能にする */
}

.container {
  flex: 1; /* wrapがフルスペースを使用 */
  display: flex;
  flex-direction: column;
  background-color: #fff;
}

/* メインコンテンツ */
.main {
  padding: 20px;
  box-sizing: border-box;
  margin-top: 60px;
  margin-right: 0; /* 余白なくす */
  position: relative;
  padding-bottom: 20px;
  border-right: none; /* 波線消し */
  width: 100%; /* 親幅に合わせる */
}

.no-border {
  border-right: none; /* ボーダーを非表示 */
}

.br {
  padding-bottom: 20px; /* フッター対策 */
}

/* タブレットサイズ以下でタイトルを画像下に表示 */
@media screen and (max-width: 800px) {
  .main {
    padding: 20px; /* 内側の余白 */
    box-sizing: border-box; /* パディングを幅に含める */
    margin-right: 0; /* 右側のマージンを0に設定 */
    width: 100%; /* 幅をフルに設定 */
    border-right: none; /* 薄い破線を非表示に設定 */
  }
}

/* スマホサイズ以下でタイトルを画像下に表示 */
@media screen and (max-width: 480px) {
}
</style>
