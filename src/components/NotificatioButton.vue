<script setup lang="ts">
//ProfileEditPage.vue
import { ref, defineProps, watch, onMounted } from 'vue'
import { useUserStore } from '@/stores/user'
import IconButton from '@/basics/IconButton.vue'

const props = defineProps<{
  n_id: number
}>()

const isFollowed = ref(false)
const isFetching = ref(true)
const showFollow = ref(false)
const responseData = ref<number[]>([])
const followerNum = ref('')
const followId = ref('')
const userStore = useUserStore()

// Piniaから ユニークなid を取得
followerNum.value = userStore.id ?? 0

const fetchLatestFollowStatus = async () => {
  try {
    const url = `https://yellowokapi2.sakura.ne.jp/Vue/api/FollowNotifyGetAPI.php/${followerNum.value}`
    console.log(`リクエストURL (fetchLatestFollowStatus): ${url}`)

    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id: props.n_id }),
    })

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`)
    }

    const responseJson = await response.json()
    console.log('APIレスポンス (FollowGetAPI):', responseJson)

    responseData.value = Array.isArray(responseJson.followed_id)
      ? responseJson.followed_id.map(Number) // 数値配列に変換
      : []

    // 型を合わせたうえで includes 判定
    isFollowed.value = responseData.value.includes(Number(props.n_id))
    console.log('最新のフォロー状態 (isFollowed):', isFollowed.value)
  } catch (error) {
    console.error('Error fetching latest follow status:', error)
  }
}

let isProcessing = false

const toggleNotify = async () => {
  if (isProcessing) return
  isProcessing = true

  try {
    await fetchLatestFollowStatus()
    console.log('Before toggle, isFollowed:', isFollowed.value)

    const actionValue = isFollowed.value ? 'delete' : 'insert'
    console.log(`トグル処理: ${actionValue}`)

    const likeurl = `https://yellowokapi2.sakura.ne.jp/Vue/api/FollowToggleAPI.php/${followId.value}/${actionValue}`
    console.log(`リクエストURL (fetchLatestToggleStatus): ${likeurl}`)

    const response = await fetch(likeurl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id: props.n_id }),
    })

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`)
    }

    const responseJson = await response.json()
    if (responseJson.success) {
      await fetchLatestFollowStatus()
      console.log('成功:', responseJson.success)
    } else {
      console.error('エラー: ' + JSON.stringify(responseJson))
    }
  } catch (error) {
    console.error('Error posting data:', error)
  } finally {
    isProcessing = false
  }
}

watch(
  () => props.n_id,
  async followedVal => {
    if (
      typeof followedVal === 'number' &&
      followedVal > 0 &&
      followerNum.value !== ''
    ) {
      if (Number(followerNum.value) !== followedVal) {
        showFollow.value = true
        followId.value = `${followerNum.value}/${followedVal}`
        await fetchLatestFollowStatus()
      } else {
        showFollow.value = false
      }
    }
  },
  { immediate: true },
)

onMounted(async () => {
  if (props.n_id > 0 && followerNum.value !== '') {
    followId.value = `${props.n_id}/${followerNum.value}`
    await fetchLatestFollowStatus()
    isFetching.value = false
  }
})
</script>

<template>
  <div><IconButton label="通知オフ" icon-class="fa-solid fa-bell-slash" /></div>
</template>

<style scoped></style>
