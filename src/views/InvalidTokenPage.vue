<script setup lang="ts">
import MessageCard from '@/components/MessageCard.vue'

function handleBack() {
  // 例：router.push('/') とか
  alert('戻るボタンが押されたよ')
}
</script>

<template>
  <MessageCard
    title="トークンエラー"
    msg="この登録用リンクは無効、または有効期限が切れています。
お手数ですが、もう一度仮登録メールアドレスの入力からやり直してください。
※すでに本登録を完了されている場合は、ログインページからアクセスしてください。"
    buttonText="戻る"
    :onClick="handleBack"
  />
</template>
