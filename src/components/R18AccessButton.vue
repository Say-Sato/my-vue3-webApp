<script setup lang="ts">
import Certificate18Modal from '@/components/Certificate18Modal.vue'
import { useAgeRestriction } from '@/composables/useAgeRestriction'

const props = defineProps<{
  postId: number
}>()

const { showModal, showR18Modal, handleConfirm, handleClose } =
  useAgeRestriction()
</script>

<template>
  <div>
    <div class="blur-overlay" @click="showR18Modal(props.postId)">
      <span class="blur-text">閲覧注意</span>
    </div>
    <Teleport to="body">
      <Certificate18Modal
        v-if="showModal"
        @confirm="handleConfirm"
        @cancel="handleClose"
      />
    </Teleport>
  </div>
</template>

<style scoped>
.blur-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.4);
  cursor: pointer; /* クリック可能であることを示す */
}

.blur-text {
  color: #fff;
  font-size: 18px;
  font-weight: bold;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
}
</style>
