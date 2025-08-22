<script setup lang="ts">
import Hamburger from '@/components/HamburgerMenu.vue' // ハンバーガーメニュー
import NotificationBell from '@/basics/NotificationBell.vue'
import UserAvatar from '@/basics/UserAvatar.vue'
import { ref } from 'vue'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()
const loginid = ref(userStore.id)

if (!loginid.value) {
  loginid.value = '0'
}
</script>

<template>
  <div>
    <!-- loginidが取得できた場合、その値を使って遷移先を設定 -->
    <router-link :to="`/home/${loginid}`">
      <h1 class="title">SPACE</h1>
    </router-link>
    <NotificationBell />
    <UserAvatar />
    <Hamburger />
  </div>
</template>

<style scoped>
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

  width: 100%;
  height: 48px;
  padding: 16px 0;
  position: fixed; /* 固定位置 */
  z-index: 10; /* 高いz-indexで前面に表示 */
  left: 0;
  top: 0; /* 画面の最下部 */
  margin: 0;
  text-align: left;
  padding-left: 20px; /* 右側にずらす */
  background-color: #fff;
  box-shadow: 0px 10px 10px 0 rgb(0, 0, 0, 0.1);
}
</style>
