<script setup lang="ts">
import Hamburger from '@/components/HamburgerMenu.vue' // ハンバーガーメニュー
import NotificationBell from '@/basics/NotificationBell.vue'
import UserAvatar from '@/basics/UserAvatar.vue'
//import SearchField from '@/components/SearchField.vue'
import { ref } from 'vue'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()
const loginid = ref(userStore.id)

if (!loginid.value) {
  loginid.value = '0'
}
</script>

<template>
  <header class="header">
    <div class="header__left">
      <router-link :to="`/home/${loginid}`">
        <h1 class="title">SPACE</h1>
      </router-link>
    </div>
    <div class="header__center">
      <!--<SearchField />-->
    </div>
    <div class="header__right">
      <NotificationBell />
      <UserAvatar />
      <Hamburger />
    </div>
  </header>
</template>

<style scoped>
.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  height: 80px; /* 適切な高さに調整 */
  padding: 0 20px;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 10;
  background-color: #fff;
  box-shadow: 0px 10px 10px 0 rgb(0, 0, 0, 0.1);
  box-sizing: border-box; /* padding を含めて width 100% */
}

.header__center {
  flex-grow: 1;
  display: flex;
  justify-content: center;
  margin: 0 20px; /* 左右との余白 */
}

.header__right {
  display: flex;
  align-items: center;
  gap: 15px; /* アイコン間の余白 */
}

.title {
  /* フォント設定 */
  font-family: 'Dela Gothic One', sans-serif;
  font-weight: 400;
  font-style: normal;
  color: #fff;
  /* 縁取り */
  text-shadow:
    1px 1px 0 #000,
    -1px -1px 0 #333,
    -1px 1px 0 #333,
    1px -1px 0 #333,
    0 1px 0 #333,
    0 -1px 0 #333,
    -1px 0 0 #333,
    1px 0 0 #333;
  margin: 0; /* 親要素のflex設定で中央揃えになるので不要なmarginは消す */
  font-size: 1.8rem; /* 元の見た目に合わせる */
}

a {
  text-decoration: none;
}
</style>
