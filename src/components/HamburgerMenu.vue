<script lang="ts">
import { defineComponent } from 'vue'
import AdminLink from '@/basics/AdminLink.vue'
import Icon from '@/components/HamburgerMenuIcon.vue'

export default defineComponent({
  name: 'HamburgerMenu',
  components: {
    AdminLink,
    Icon,
  },
  data() {
    return {
      isOpen: false, // メニューが開いているかどうかの状態
    }
  },
  methods: {
    toggleMenu() {
      this.isOpen = !this.isOpen // メニューの開閉を切り替える
    },
    handleClickOutside(event: MouseEvent) {
      const menu = this.$refs.menu as HTMLElement
      if (this.isOpen && menu && !menu.contains(event.target as Node)) {
        this.isOpen = false
      }
    },
  },
})
</script>

<template>
  <div id="app" @click="handleClickOutside">
    <button @click.stop="toggleMenu" class="hamburger">
      <Icon :isOpen="isOpen" @toggle-menu="toggleMenu" />
    </button>

    <AdminLink :isOpen="isOpen" ref="menu" @click="toggleMenu" />

    <div :class="['overlay', { show: isOpen }]" @click="toggleMenu"></div>
  </div>
</template>

<style scoped>
.hamburger {
  background: none; /* 背景なし */
  border: none; /* ボーダーなし */
  cursor: pointer; /* ポインタを表示 */
  position: fixed; /* 固定位置 */
  top: 20px; /* 上からの位置調整 */
  right: 30px; /* 右からの位置調整 */
  z-index: 1000; /* 他の要素の上に表示 */
}

.overlay {
  position: fixed; /* 固定位置 */
  top: 0; /* 上端に配置 */
  left: 0; /* 左端に配置 */
  width: 100%; /* 幅を100%に設定 */
  height: 100%; /* 高さを100%に設定 */
  background: rgba(0, 0, 0, 0.5); /* 半透明の黒色オーバーレイ */
  z-index: 998; /* メニューの下に表示 */
  opacity: 0; /* 初期状態で透明 */
  pointer-events: none; /* 初期状態ではクリック不可 */
  transition: opacity 0.3s ease; /* フェードイン/アウト */
}

.overlay.show {
  opacity: 1; /* オーバーレイが表示されるときの透明度 */
  pointer-events: auto; /* オーバーレイをクリック可能にする */
}
</style>
