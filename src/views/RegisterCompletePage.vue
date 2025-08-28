<script setup lang="ts">
import MessageCard from '@/components/MessageCard.vue'

function handleBack() {
  // 例：router.push('/') とか
  alert('戻るボタンが押されたよ')
}
</script>

<template>
  <MessageCard
    title="仮タイトル"
    msg="登録が完了しました！！！！！！。"
    buttonText="戻る"
    :onClick="handleBack"
  />
</template>

<style scoped></style>
