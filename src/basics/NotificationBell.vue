<script setup lang="ts">
import { ref } from 'vue'

const Notification = ref(false)
</script>

<template>
  <div class="ellipse-wrapper">
    <div class="ellipse-2"></div>
    <div class="badge" v-if="Notification"></div>
  </div>
</template>

<style scoped>
.ellipse-wrapper {
  position: fixed;
  width: 30px;
  height: 30px;
  top: 27px;
  right: 144px;
  background-image: url('..\assets\images\doorbell.png');
  background-size: 100% 100%;
  cursor: pointer;
  z-index: 995;
}

.ellipse-2 {
  position: absolute;
  width: 1px;
  height: 1px;
  top: 0;
  right: 0;
  background-color: var(--x1-primary-color);
  border-radius: 50%;
}

.badge {
  position: absolute;
  top: 0;
  right: 0;
  width: 12px;
  height: 12px;
  background-color: red;
  border-radius: 50%;
}

@media (max-width: 768px) {
  .ellipse-wrapper {
    right: 122px;
  }
}
</style>
