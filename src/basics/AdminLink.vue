<script setup lang="ts">
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()
//isLigin = *ログインしているときのみtrue*

defineProps({
  isOpen: Boolean, // メニューの開閉状態を親から受け取る
})

defineEmits(['close']) // 外部クリックで閉じるイベント
</script>

<template>
  <nav :class="{ open: isOpen }" ref="menu">
    <ul>
      <li><router-link to="/about">about</router-link></li>
      <li><router-link to="/test">testPage</router-link></li>
      <span class="login" v-if="userStore.isLogin">
        <li><router-link to="/article">記事一覧</router-link></li>
      </span>
      <li><router-link to="/login">ログイン</router-link></li>
      <li><router-link to="/publish">記事投稿</router-link></li>
      <span class="login" v-if="userStore.isLogin"><li>ログアウト</li></span>
    </ul>
  </nav>
</template>

<style scoped>
nav {
  display: block; /* メニューは初めから存在するが、visibilityで制御 */
  background-color: #fff; /* メニューの背景色 */
  position: absolute; /* メニューの位置を絶対指定 */
  right: 0; /* 右端に配置 */
  width: 310px; /* メニューの幅 */
  height: 100%;
  z-index: 999; /* ハンバーガーボタンの下に表示 */
  position: fixed; /* 固定位置 */
  bottom: 0px;
  opacity: 0; /* 初期状態で非表示（透明） */
  pointer-events: none; /* メニュー表示時はクリックできない */
  visibility: hidden; /* メニューが非表示 */
  transition:
    opacity 0.3s ease,
    visibility 0s 0.3s; /* フェードイン、フェードアウトアニメーション */
}

nav.open {
  opacity: 1; /* メニューが表示されたときに不透明に */
  pointer-events: auto; /* メニュー表示時にクリックできるように */
  visibility: visible; /* メニューが表示されたときに可視化 */
  transition:
    opacity 0.3s ease,
    visibility 0s 0s; /* フェードインアニメーション */
}

.search {
  position: relative;
  left: 5px;
}

nav ul {
  list-style-type: none; /* リストのスタイルをなしに */
  padding: 0; /* パディングなし */
  width: fit-content; /* 内容に合わせた幅 */
  margin: auto; /* 中央に配置 */
  margin-top: 100px; /* 10px下げるためのマージン */
}

nav li {
  padding: 10px; /* リストアイテムのパディング */
}

nav a {
  color: #333; /* リンクの文字色 */
  text-decoration: none; /* 下線なし */
}

nav a:hover {
  text-decoration: underline; /* ホバー時に下線を表示 */
}

/* タブレットサイズ以下でタイトルを画像下に表示 */
@media screen and (max-width: 768px) {
  nav {
    width: 100%; /* メニューの幅をフルに設定 */
  }
}
</style>
