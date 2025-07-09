<script setup lang="ts">
import { defineProps } from 'vue'
import IconButton from '@/basics/IconButton.vue'

const props = defineProps<{
  prevId: number
  nextId: number
}>()
</script>

<template>
  <div class="nav-wrapper">
    <!-- 前のボタンスペース -->
    <div class="button-wrapper">
      <template v-if="props.prevId !== 0">
        <router-link :to="`/article/${props.prevId}`">
          <IconButton
            label="前の記事"
            icon-class="fa-solid fa-arrow-left"
            background-color="#cccccc"
            textColor="white"
          />
        </router-link>
      </template>
    </div>

    <!-- 真ん中の縦線 -->
    <div class="divider"></div>

    <!-- 次のボタンスペース -->
    <div class="button-wrapper">
      <template v-if="props.nextId !== 0">
        <router-link :to="`/article/${props.nextId}`">
          <IconButton
            label="次の記事"
            icon-class="fa-solid fa-arrow-right"
            background-color="#cccccc"
            textColor="white"
          />
        </router-link>
      </template>
    </div>
  </div>
</template>

<style scoped>
.nav-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0px;
  margin-top: 16px;
}

/* ボタン用の固定幅の箱。非表示時もスペース確保 */
.button-wrapper {
  width: 150px; /* アイコンボタンの幅に合わせて調整してね */
  display: flex;
  justify-content: center;
  align-items: center;
}

/* 縦線 */
.divider {
  width: 1px;
  background-color: #ccc;
  height: 40px; /* ボタンの高さに合わせて調整してね */
}
</style>
