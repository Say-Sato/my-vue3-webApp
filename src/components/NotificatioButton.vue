<script setup lang="ts">
//ProfileEditPage.vue
import { ref, defineProps, watch } from 'vue'
import { useUserStore } from '@/stores/user'
import IconButton from '@/basics/IconButton.vue'

const props = defineProps<{
  n_id: number
}>()

const isNotify = ref(false)
const isFetching = ref(true)
const showFollow = ref(false)
const userNum = ref('')
const followId = ref('')
const userStore = useUserStore()

// Piniaから ユニークなid を取得
userNum.value = userStore.id ?? 0

const fetchLatestNotifyStatus = async () => {
  try {
    const url = `https://yellowokapi2.sakura.ne.jp/Vue/api/FollowNotifyGetAPI.php/${followId.value}`
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
    console.log('APIレスポンス (FollowNotifyGetAPI):', responseJson)

    if (responseJson.success) {
      // NotifyFrag 0 → 通知オン(true), 1 → 通知オフ(false)
      isNotify.value = Number(responseJson.NotifyFrag) === 0
      console.log('最新のフォロー通知状態 (isNotify):', isNotify.value)
    }
  } catch (error) {
    console.error('Error fetching latest follow status:', error)
  }
}

let isProcessing = false

const toggleNotify = async () => {
  if (isProcessing) return
  isProcessing = true

  try {
    await fetchLatestNotifyStatus()
    console.log('Before toggle, isNotify:', isNotify.value)

    const actionValue = isNotify.value ? 'insert' : 'delete'
    console.log(`トグル処理: ${actionValue}`)

    const Toggleurl = `https://yellowokapi2.sakura.ne.jp/Vue/api/NotifyToggleAPI.php/${followId.value}/${actionValue}`
    console.log(`リクエストURL (fetchLatestToggleStatus): ${Toggleurl}`)

    const response = await fetch(Toggleurl, {
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
    console.log('APIレスポンス (NotifyToggleAPI):', responseJson)

    if (responseJson.success) {
      // status を見て最終状態を決定
      isNotify.value = responseJson.status === '通知オン'
      console.log('トグル後のフォロー通知状態 (isNotify):', isNotify.value)
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
  async newNId => {
    isFetching.value = true
    if (typeof newNId === 'number' && newNId > 0 && userNum.value) {
      if (Number(userNum.value) !== newNId) {
        showFollow.value = true
        followId.value = `${userNum.value}/${newNId}`
        await fetchLatestNotifyStatus()
      } else {
        showFollow.value = false
      }
    }
    isFetching.value = false
  },
  { immediate: true },
)
</script>

<template>
  <div>
    <IconButton
      :label="isNotify ? '通知オフ' : '通知オン'"
      :iconClass="isNotify ? 'fa-solid fa-bell-slash' : 'fa-solid fa-bell'"
      :backgroundColor="isNotify ? 'primary' : 'secondary'"
      @click="toggleNotify"
    />
  </div>
</template>
