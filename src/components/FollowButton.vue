<script setup lang="ts">
import { ref, defineProps, watch, onMounted } from 'vue'
import IconButton from '@/basics/IconButton.vue'
import { useUserStore } from '@/stores/user'

const props = defineProps<{
  f_id: number
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
    const url = `https://yellowokapi2.sakura.ne.jp/Vue/api/FollowGetAPI.php/${followerNum.value}`
    console.log(`リクエストURL (fetchLatestFollowStatus): ${url}`)

    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id: props.f_id }),
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
    isFollowed.value = responseData.value.includes(Number(props.f_id))
    console.log('最新のフォロー状態 (isFollowed):', isFollowed.value)
  } catch (error) {
    console.error('Error fetching latest follow status:', error)
  }
}

let isProcessing = false

const toggleFollow = async () => {
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
      body: JSON.stringify({ id: props.f_id }),
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
  () => props.f_id,
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
  if (props.f_id > 0 && followerNum.value !== '') {
    followId.value = `${props.f_id}/${followerNum.value}`
    await fetchLatestFollowStatus()
    isFetching.value = false
  }
})
</script>

<template>
  <head>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
  </head>
  <div class="liked-container" v-if="showFollow">
    <IconButton
      :label="isFollowed ? 'フォロー中' : 'フォロー'"
      :iconClass="
        isFollowed ? 'fa-solid fa-user-check' : 'fa-solid fa-user-plus'
      "
      color="white"
      :backgroundColor="isFollowed ? '#ccc' : '#1e1e1e'"
      textColor="white"
      @click="toggleFollow"
    />
  </div>
</template>

<style scoped>
button {
  width: 120px; /* 任意の横幅を設定 */
}
</style>
