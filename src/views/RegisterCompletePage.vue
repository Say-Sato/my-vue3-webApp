<script setup lang="ts">
import MessageCard from '@/components/MessageCard.vue'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'

const sec = ref(10)
let timer: number | null = null

const router = useRouter()
const msg = ref(
  '登録が完了しました。ページは' +
    sec.value +
    '秒後に遷移します。ページが自動的に遷移しない場合は下のボタンを押して下さい。',
)

function handleBack() {
  // 例：router.push('/') とか
  router.push(`/home`)
}

onMounted(() => {
  timer = window.setInterval(() => {
    if (sec.value > 0) {
      sec.value--
    } else {
      clearInterval(timer!)
      handleBack()
    }
  }, 1000)
})

onBeforeUnmount(() => {
  if (timer) clearInterval(timer)
})
</script>

<template>
  <MessageCard
    title="登録完了"
    :msg="msg"
    buttonText="ホームへ戻る"
    :onClick="handleBack"
  />
</template>
