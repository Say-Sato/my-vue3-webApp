<script setup lang="ts">
import { ref, defineProps, watch, onMounted } from 'vue'
import IconButton from '@/basics/IconButton.vue'
import { useUserStore } from '@/stores/user'

const props = defineProps<{
  n_id: number
}>()
const isNotifyed = ref(false)
const isFetching = ref(true)
const userNum = ref('')
const NotifyId = ref('')
const userStore = useUserStore()

// Piniaから ユニークなid を取得
userNum.value = userStore.id ?? '0'

const fetchLatestNotifyStatus = async () => {
  try {
    const url = `https://yellowokapi2.sakura.ne.jp/Vue/api/FollowNotifyGetAPI.php/${NotifyId.value}`

    const response = await fetch(url)

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`)
    }

    const responseJson = await response.json()
    console.log('APIレスポンス (FollowNotifyGetAPI):', responseJson)

    if (responseJson.success) {
      // NotifyFrag 0 → 通知オン(true), 1 → 通知オフ(false)
      isNotifyed.value = Number(responseJson.NotifyFrag) === 0
      console.log('最新のフォロー通知状態 (isNotify):', isNotifyed.value)
    }
  } catch (error) {
    console.error('Error fetching latest notify status:', error)
  }
}

let isProcessing = false

const toggleNotify = async () => {
  if (isProcessing) return
  isProcessing = true

  try {
    // API仕様: 通知オン(isNotifyed:true)の時にブロックテーブルにレコードを追加(insert)して通知をオフにする
    // 通知オフ(isNotifyed:false)の時にブロックテーブルからレコードを削除(delete)して通知をオンにする
    const actionValue = isNotifyed.value ? 'insert' : 'delete'

    const notifyUrl = `https://yellowokapi2.sakura.ne.jp/Vue/api/NotifyToggleAPI.php/${NotifyId.value}/${actionValue}`

    const response = await fetch(notifyUrl)

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`)
    }

    const responseJson = await response.json()
    if (responseJson.success) {
      await fetchLatestNotifyStatus()
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
  async newVal => {
    if (typeof newVal === 'number' && newVal > 0 && userNum.value !== '') {
      // APIのパスパラメータの順序: /user_id/notify_id
      NotifyId.value = `${userNum.value}/${newVal}`
      await fetchLatestNotifyStatus()
    }
  },
  { immediate: true },
)

onMounted(async () => {
  if (props.n_id > 0 && userNum.value !== '') {
    // APIのパスパラメータの順序: /user_id/notify_id
    NotifyId.value = `${userNum.value}/${props.n_id}`
    await fetchLatestNotifyStatus()
    isFetching.value = false
  }
})
</script>

<template>
  <div>
    <IconButton
      :label="isNotifyed ? '通知オン' : '通知オフ'"
      :iconClass="isNotifyed ? 'fa-solid fa-bell' : 'fa-solid fa-bell-slash'"
      :backgroundColor="isNotifyed ? 'secondary' : 'primary'"
      @click="toggleNotify"
    />
  </div>
</template>
