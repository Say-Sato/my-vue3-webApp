<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useUserStore } from '@/stores/user'
import { useRoute } from 'vue-router'

// TypeScriptインターフェースの定義
interface Notification {
  type: 'comments' | 'follows' | 'favorite' | 'illust'
  id: number | string
  created_at: string
  actor: string
  target: string | null
}

const showNotifications = ref(false)
const notifications = ref<Notification[]>([])
const isLoading = ref(false)
const userStore = useUserStore()
const route = useRoute()
const notificationContainer = ref<HTMLElement | null>(null)
const lastChecked = ref(userStore.last_checked || '')

// バッジ表示用のフラグ
const showBadge = ref(false)

// バッジの状態を更新する関数
const updateBadgeState = () => {
  showBadge.value = notifications.value.length > 0
}

// 通知の有無をチェックしてバッジ表示を更新する関数
const checkNotifications = async () => {
  if (userStore.isLogin) {
    isLoading.value = true
    try {
      const userId = userStore.id

      if (!userId) {
        alert('ログインしてください！！')
      } else {
        const response = await fetch(
          `https://yellowokapi2.sakura.ne.jp/Vue/api/NotificationFetchAPI.php?user_id=${userId}&last_checked=${encodeURIComponent(lastChecked.value)}`,
          {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
            },
          },
        )

        if (!response.ok) throw new Error('Network response was not ok')
        const data = await response.json()
        if (data.success) {
          notifications.value = data.notifications
        } else {
          notifications.value = []
        }
        console.log('レスポンス内容：' + data.last_checked)
        updateBadgeState() // バッジ状態更新！
      }
    } catch (error) {
      console.error('Failed to check notifications:', error)
      notifications.value = []
      updateBadgeState() // 通知が空でも状態更新
    } finally {
      isLoading.value = false
    }
  }
}

// 現在日時のフォーマットを修正
const formatDateTime = (date: Date): string => {
  const pad = (n: number) => String(n).padStart(2, '0')

  const year = date.getFullYear()
  const month = pad(date.getMonth() + 1)
  const day = pad(date.getDate())
  const hours = pad(date.getHours())
  const minutes = pad(date.getMinutes())
  const seconds = pad(date.getSeconds())

  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`
}

// 通知リストの表示を切り替えてデータを取得する関数
const toggleNotifications = async () => {
  showNotifications.value = !showNotifications.value
  // リストを開くときに最新の情報を取得する
  if (showNotifications.value) {
    await checkNotifications()
    lastChecked.value = formatDateTime(new Date())
    userStore.last_checked = lastChecked.value
    console.log('userStore: ' + userStore.last_checked)
    showBadge.value = false
  }
}

// 外側をクリックしたときに通知リストを閉じる
const handleClickOutside = (event: MouseEvent) => {
  if (
    notificationContainer.value &&
    !notificationContainer.value.contains(event.target as Node)
  ) {
    showNotifications.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  checkNotifications() // マウント時に通知をチェック
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// ルートの変更を監視して通知をチェック
watch(
  () => route.path,
  () => {
    checkNotifications()
  },
)

// 通知メッセージを生成する関数
const formatMessage = (notification: Notification): string => {
  switch (notification.type) {
    case 'comments':
      return `${notification.actor}があなたの投稿「${notification.target}」にコメントしました。`
    case 'follows':
      return `${notification.actor}があなたをフォローしました。`
    case 'favorite':
      return `${notification.actor}があなたの投稿「${notification.target}」をいいねしました。`
    case 'illust':
      return `フォローしている${notification.actor}が新作「${notification.target}」を投稿しました。`
    default:
      return '新しい通知があります。'
  }
}
</script>

<template>
  <div ref="notificationContainer" class="notification-container">
    <div class="ellipse-wrapper" @click="toggleNotifications">
      <div class="ellipse-2"></div>
      <div class="badge" v-if="showBadge"></div>
    </div>

    <div v-if="showNotifications" class="notification-list">
      <div v-if="isLoading" class="loading">読み込み中...</div>
      <div v-else-if="notifications.length === 0" class="no-notifications">
        新しい通知はありません。
      </div>
      <div v-else>
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="notification-item"
        >
          <p>{{ formatMessage(notification) }}</p>
          <span class="notification-time">{{
            new Date(notification.created_at).toLocaleString()
          }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.notification-container {
  position: relative;
  z-index: 995;
}

.ellipse-wrapper {
  position: fixed;
  width: 30px;
  height: 30px;
  top: 27px;
  right: 144px;
  background-image: url('..\assets\images\doorbell.png');
  background-size: 100% 100%;
  cursor: pointer;
}

.badge {
  position: absolute;
  top: 0;
  right: 0;
  width: 12px;
  height: 12px;
  background-color: red;
  border-radius: 50%;
}

.notification-list {
  position: fixed;
  top: 70px; /* ヘッダーの高さに応じて調整 */
  right: 40px;
  width: 350px;
  max-height: 400px;
  overflow-y: auto;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.loading,
.no-notifications {
  padding: 20px;
  text-align: center;
  color: #666;
}

.notification-item {
  padding: 12px 15px;
  border-bottom: 1px solid #eee;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-item p {
  margin: 0;
  font-size: 14px;
  color: #333;
}

.notification-item .notification-time {
  font-size: 12px;
  color: #999;
  display: block;
  margin-top: 4px;
}

@media (max-width: 768px) {
  .ellipse-wrapper {
    right: 122px;
  }
  .notification-list {
    width: 300px;
    right: 20px;
  }
}
</style>
