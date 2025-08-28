<script setup lang="ts">
import ModalComponent from '@/components/ConfirmModal.vue'
import { useUserStore } from '@/stores/user'
import { useRouter } from 'vue-router'

import { ref } from 'vue'
const isModalVisible = ref(false) // モーダルの表示状態を管理
const modalMessage = ref('本当にログアウトしますか？')
const modalConfirmText = ref('ログアウト')
const modalCancelText = ref('キャンセル')
const router = useRouter()

const showModal = (event: Event) => {
  event.stopPropagation() // クリック伝播を止める
  isModalVisible.value = true
}

const onModalConfirm = () => {
  const userStore = useUserStore()

  isModalVisible.value = false

  // userStoreの初期化
  userStore.logout()

  // ルーターで指定ページへ遷移
  router.push('/home') // 任意の遷移先に変えてね
}

const onModalCancel = () => {
  isModalVisible.value = false
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
