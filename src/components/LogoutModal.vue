<script lang="ts">
import ModalComponent from '@/components/ConfirmModal.vue'
import { useUserStore } from '@/stores/user'

export default {
  name: 'ArticleDeleteComponent',
  components: {
    ModalComponent,
  },
  data() {
    return {
      isModalVisible: false, // モーダルの表示状態を管理
      modalMessage: '本当にログアウトしますか？',
      modalConfirmText: 'ログアウト',
      modalCancelText: 'キャンセル',
    }
  },
  methods: {
    showModal(event: Event) {
      event.stopPropagation() // クリック伝播を止める
      this.isModalVisible = true
    },
    onModalConfirm() {
      const userStore = useUserStore()

      this.isModalVisible = false

      // userStoreの初期化
      userStore.logout()

      // ルーターで指定ページへ遷移
      this.$router.push('/home') // 任意の遷移先に変えてね
    },

    onModalCancel() {
      this.isModalVisible = false
    },
  },
}
</script>

<template>
  <li class="nav-item">
    <!-- クリックイベントを stopPropagation 付きでバインド -->
    <span class="nav-link" @click="showModal">ログアウト</span>
  </li>

  <ModalComponent
    v-if="isModalVisible"
    :message="modalMessage"
    :confirmText="modalConfirmText"
    :cancelText="modalCancelText"
    :isVisible="isModalVisible"
    :onConfirm="onModalConfirm"
    :onCancel="onModalCancel"
  />
</template>
