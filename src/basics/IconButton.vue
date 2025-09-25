<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    label?: string
    iconClass?: string
    backgroundColor?:
      | 'primary'
      | 'secondary'
      | 'success'
      | 'danger'
      | 'warning'
      | 'favorite'
    color?: string
    textColor?: string
    type?: 'button' | 'submit' | 'reset'
  }>(),
  {
    label: 'Search',
    iconClass: '',
    backgroundColor: 'primary',
    color: 'white',
    textColor: 'white',
    type: 'button',
  },
)

const colorClass = computed(() => {
  return props.backgroundColor
    ? `btn--${props.backgroundColor}`
    : 'btn--primary'
})
</script>

<template>
  <head>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
  </head>
  <button
    class="icon-button"
    :class="colorClass"
    :type="props.type"
    :style="{ color: props.textColor }"
    v-bind="$attrs"
  >
    <i :class="props.iconClass" :style="{ color: color }"></i>
    {{ props.label }}
  </button>
</template>

<style scoped>
.icon-button {
  border: none;
  border-radius: 15px; /* 角を丸める */
  padding: 6px 12px 8px; /* 内側の余白を設定 */
  font-size: 14px; /* フォントサイズ */
  min-width: 7em;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* ほんのり影をつける */
  transition:
    background-color 0.3s ease,
    box-shadow 0.3s ease; /* 背景色と影のアニメーション */
  cursor: pointer;
}

.icon-button:active {
  background-color: #d0d0d0 !important;
  box-shadow: 0 2px 6px rgba(229, 52, 140, 0.6);
  transform: translateY(2px);
}

.btn--primary {
  background-color: #1e1e1e;
}
.btn--secondary {
  background-color: #cbcbcb;
}
.btn--success {
  background-color: #22c55e;
}
.btn--danger {
  background-color: #ef4444;
}
.btn--warning {
  background-color: #f59e0b;
}
.btn--favorite {
  background-color: #e5348c;
}
</style>
