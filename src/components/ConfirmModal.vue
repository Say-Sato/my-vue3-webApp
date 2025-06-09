<script lang="ts">
export default {
  props: {
    message: {
      type: String,
      default: 'テストメッセージ', // 文言の初期値
    },
    confirmText: {
      type: String,
      default: '決定', // 右ボタン文言の初期値
    },
    isVisible: {
      type: Boolean,
      required: true, // モーダルの表示状態を親から渡す
    },
    onConfirm: {
      type: Function,
      default: () => {}, // デフォルトは何もしない関数
    },
    onCancel: {
      type: Function,
      default: () => {}, // キャンセル時のデフォルト動作
    },
  },
  methods: {
    handleCancel() {
      this.onCancel() // 親から渡されたキャンセルイベントを実行
    },
    handleConfirm() {
      this.onConfirm() // 親から渡された確認イベントを実行
    },
  },
}
</script>

<template>
  <div class="modal-overlay" v-if="isVisible">
    <div class="modal-content">
      <p>{{ message }}</p>
      <div class="modal-buttons">
        <button @click="handleCancel">戻る</button>
        <button @click="handleConfirm">{{ confirmText }}</button>
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
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.modal-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

button {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

button:first-of-type {
  background: #ccc;
}

button:last-of-type {
  background: #007bff;
  color: white;
}
</style>
