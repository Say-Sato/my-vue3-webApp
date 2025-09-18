<!-- UserPage etc. 汎用ギャラリー -->
<script setup lang="ts">
import { ref, defineEmits } from 'vue'
import Modal from '@/components/ConfirmModal.vue'

const isModal = ref(true)
const msg = '年齢制限のある画像を表示しますか？'

const emit = defineEmits<{
  (e: 'close'): void
}>()

const enterModal = () => {
  emit('close')
}

const cancelmodal = () => {
  emit('close')
}
</script>

<template>
  <Modal
    :message="msg"
    confirmText="表示する"
    cancelText="戻る"
    :isVisible="isModal"
    :onConfirm="enterModal"
    :onCancel="cancelmodal"
  />
</template>
