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
  <nav :class="['sidebar-nav', { open: isOpen }]" ref="menu">
    <ul class="nav-menu" role="list">
      <span class="login" v-if="userStore.isLogin">
        <li class="nav-item">
          <router-link class="nav-link" to="/posts/new"
            >イラスト投稿</router-link
          >
        </li>
        <li class="nav-item">
          <router-link class="nav-link" to="/posts">自分のイラスト</router-link>
        </li>
        <li class="nav-item">
          <span class="nav-link">いいね一覧</span>
        </li>
        <li class="nav-item">
          <router-link class="nav-link" to="/profile">設定</router-link>
        </li>
        <li class="nav-item">
          <span class="nav-link">ログアウト</span>
        </li>
      </span>

      <span class="login" v-else>
        <li class="nav-item">
          <span class="nav-link"
            >全ての機能を利用するにはログインが必要です。</span
          >
        </li>
        <li class="nav-item">
          <span class="nav-link">
            ログインは<router-link to="/login">こちら</router-link>
          </span>
        </li>
      </span>
    </ul>
  </nav>
</template>

<style scoped>
.sidebar-nav {
  display: block;
  background-color: #ffffff;
  position: fixed;
  right: 0;
  top: 0;
  width: 308px;
  height: 930px;
  z-index: 999;
  opacity: 0;
  pointer-events: none;
  visibility: hidden;
  transition:
    opacity 0.3s ease,
    visibility 0s 0.3s;
}

.sidebar-nav.open {
  opacity: 1;
  pointer-events: auto;
  visibility: visible;
  transition:
    opacity 0.3s ease,
    visibility 0s 0s;
}

.nav-menu {
  position: relative;
  width: 142px;
  height: 250px;
  top: 100px;
  left: 88px;
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-item {
  position: relative;
  margin-bottom: 26px;
}

.nav-item:last-child {
  margin-bottom: 0;
}

.nav-link,
.nav-button {
  position: relative;
  width: 132px;
  left: 0;
  font-weight: 400;
  color: #000000;
  font-size: 16px;
  line-height: normal;
  letter-spacing: 0;
  text-decoration: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  text-align: left;
  display: block;
}

.nav-link:hover,
.nav-button:hover {
  opacity: 0.7;
}

.nav-link:focus,
.nav-button:focus {
  outline: 2px solid #4a90e2;
  outline-offset: 2px;
}
/* タブレットサイズ以下でタイトルを画像下に表示 */
@media screen and (max-width: 768px) {
  .sidebar-nav {
    width: 100%; /* メニューの幅をフルに設定 */
  }
}
</style>
