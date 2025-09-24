<script setup lang="ts">
import MessageCard from '@/components/MessageCard.vue'

function handleBack() {
  window.close()
}
</script>

<template>
  <MessageCard
    title="登録完了"
    msg="入力されたメールアドレスに本登録用リンクを送信しました。このページを閉じてメールを確認してください。ボタンが反応しない場合は右上の×ボタンを押してタブを閉じてください。"
    buttonText="閉じる"
    :onClick="handleBack"
  />
</template>
