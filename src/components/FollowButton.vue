<script setup lang="ts">
import { ref, defineProps, watch, onMounted } from 'vue'
import IconButton from '@/basics/IconButton.vue'
import { useUserStore } from '@/stores/user'

const props = defineProps<{
  f_id: number
}>()
const isFollowed = ref(false)
const isFetching = ref(true)
const responseData = ref<number[]>([])
const followerNum = ref('')
const followId = ref('')
const userStore = useUserStore()

// Piniaから ユニークなid を取得
followerNum.value = userStore.id ?? '0'

const fetchLatestFollowStatus = async () => {
  try {
    // FollowGetAPI.phpはf_idをbodyで受け取るので、この部分は変更しない
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

    const toggleUrl = `https://yellowokapi2.sakura.ne.jp/Vue/api/FollowToggleAPI.php/${followId.value}/${actionValue}`
    console.log(`リクエストURL (toggleFollow): ${toggleUrl}`)

    // APIはURLからパラメータを取得するため、bodyは不要
    const response = await fetch(toggleUrl)

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
      // APIのパスパラメータの順序: /follower_id/followed_id
      followId.value = `${followerNum.value}/${followedVal}`
      await fetchLatestFollowStatus()
    }
  },
  { immediate: true },
)

onMounted(async () => {
  if (props.f_id > 0 && followerNum.value !== '') {
    // APIのパスパラメータの順序: /follower_id/followed_id
    followId.value = `${followerNum.value}/${props.f_id}`
    await fetchLatestFollowStatus()
    isFetching.value = false
  }
})
</script>

<template>
  <div>
    <IconButton
      :label="isFollowed ? 'フォロー中' : 'フォロー'"
      :iconClass="
        isFollowed ? 'fa-solid fa-user-check' : 'fa-solid fa-user-plus'
      "
      color="white"
      :backgroundColor="isFollowed ? 'secondary' : 'primary'"
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
