<script setup lang="ts">
import { defineProps } from 'vue'

// props定義（最初のまま！）
const props = defineProps<{
  message: string
  confirmText: string
  cancelText: string
  onConfirm: () => void
  onCancel: () => void
  isVisible: boolean
}>()

// メソッド（動作は今のコードベースに揃える）
function handleCancel() {
  props.onCancel()
}

function handleEnter() {
  props.onConfirm()
}
</script>

<template>
  <div class="modal-overlay" v-if="isVisible">
    <div class="modal-container">
      <p class="modal-message">{{ message }}</p>
      <div class="modal-buttons">
        <button class="cancel-button" @click="handleCancel">
          {{ cancelText }}
        </button>
        <button class="enter-button" @click="handleEnter">
          {{ confirmText }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal-container {
  background: white;
  border-radius: 16px;
  padding: 40px 50px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  text-align: center;
  max-width: 500px;
  width: 90%;
}

.modal-message {
  margin-bottom: 30px;
  color: #333;
  font-size: 14px;
  line-height: 1.7;
}

.modal-buttons {
  display: flex;
  justify-content: center;
  gap: 30px;
}

.cancel-button {
  padding: 10px 30px;
  background: #ccc;
  color: white;
  border: none;
  border-radius: 20px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}

.cancel-button:hover {
  background: #bbb;
}

.enter-button {
  padding: 10px 30px;
  background: #111;
  color: white;
  border: none;
  border-radius: 20px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}

.enter-button:hover {
  background: #000;
}
</style>
