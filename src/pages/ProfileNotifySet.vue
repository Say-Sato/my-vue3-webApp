<script setup lang="ts">
import { ref, watch } from 'vue'
import { useUserStore } from '@/stores/user'

import RadioInput from '@/basics/RadioInput.vue'
import IconButton from '@/basics/IconButton.vue'

const props = defineProps<{
  editNotify: boolean
  notification: notify[]
}>()
type notify = {
  comment: string
  follow: string
  favorite: string
  illust: string
}

const emit = defineEmits<{
  (e: 'submit'): void
  (e: 'update:editNotify', value: boolean): void
}>()

// ローカル状態
const localComment = ref('0')
const localFollow = ref('0')
const localFavorite = ref('0')
const localIllust = ref('0')

// フラグ（編集判定）
const isEdited = ref({
  comment: false,
  follow: false,
  favorite: false,
  illust: false,
})

// props が来たら local に代入
// notification の各項目を個別に監視
watch(
  () => props.notification[0]?.comment,
  val => {
    if (val != null) localComment.value = String(val)
  },
  { immediate: true },
)

watch(
  () => props.notification[0]?.follow,
  val => {
    if (val != null) localFollow.value = String(val)
  },
  { immediate: true },
)

watch(
  () => props.notification[0]?.favorite,
  val => {
    if (val != null) localFavorite.value = String(val)
  },
  { immediate: true },
)

watch(
  () => props.notification[0]?.illust,
  val => {
    if (val != null) localIllust.value = String(val)
  },
  { immediate: true },
)

// 変更を監視して編集フラグを立てる
watch(
  localComment,
  v => (isEdited.value.comment = v !== props.notification[0]?.comment),
)
watch(
  localFollow,
  v => (isEdited.value.follow = v !== props.notification[0]?.follow),
)
watch(
  localFavorite,
  v => (isEdited.value.favorite = v !== props.notification[0]?.favorite),
)
watch(
  localIllust,
  v => (isEdited.value.illust = v !== props.notification[0]?.illust),
)

const errorMessage = ref('')
const userStore = useUserStore()
const id = ref(userStore.id)

// 送信処理
const handleSubmit = async () => {
  const formData = new FormData()

  if (isEdited.value.comment)
    formData.append('comment', localComment.value || '0')
  if (isEdited.value.follow) formData.append('follow', localFollow.value || '0')
  if (isEdited.value.favorite)
    formData.append('favorite', localFavorite.value || '0')
  if (isEdited.value.illust) formData.append('illust', localIllust.value || '0')

  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/NotificationEditAPI.php/${id.value}`,
      { method: 'POST', body: formData },
    )

    const contentType = response.headers.get('Content-Type') || ''
    if (!contentType.includes('application/json')) {
      throw new Error('サーバーからJSON形式の返答がありませんでした。')
    }

    const result = await response.json()
    if (!response.ok)
      throw new Error(result.message || `HTTP ${response.status}`)

    alert('データが正常に送信されました')
    emit('submit')
    emit('update:editNotify', false)
  } catch (err) {
    console.error('更新失敗:', err)
    errorMessage.value = '更新に失敗しました。もう一度お試しください。'
  }
}

// キャンセルボタン処理
const closeForm = () => {
  emit('update:editNotify', false) // 親の editNotify を false にする
}
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <h1 class="notify-title">Notification</h1>
    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>

    <p>コメント</p>
    <RadioInput
      id="onComment"
      name="comment"
      value="1"
      label="通知する"
      v-model="localComment"
    />
    <RadioInput
      id="offComment"
      name="comment"
      value="0"
      label="非通知"
      v-model="localComment"
    />
    <p>フォロー</p>
    <RadioInput
      id="onFollow"
      name="follow"
      value="1"
      label="通知する"
      v-model="localFollow"
    />
    <RadioInput
      id="offFollow"
      name="follow"
      value="0"
      label="非通知"
      v-model="localFollow"
    />
    <p>いいね</p>
    <RadioInput
      id="onFavorite"
      name="favorite"
      value="1"
      label="通知する"
      v-model="localFavorite"
    />
    <RadioInput
      id="offFavorite"
      name="favorite"
      value="0"
      label="非通知"
      v-model="localFavorite"
    />
    <p>投稿</p>
    <RadioInput
      id="onIllust"
      name="illust"
      value="1"
      label="通知する"
      v-model="localIllust"
    />
    <RadioInput
      id="offIllust"
      name="illust"
      value="0"
      label="非通知"
      v-model="localIllust"
    />
    <div class="submit-notify">
      <IconButton
        label="キャンセル"
        backgroundColor="secondary"
        @click="closeForm"
      />
      <IconButton type="submit" label="保存する" />
    </div>
  </form>
</template>

<style scoped>
.notify-title {
  font-size: 28px;
  font-weight: 700;
  color: #333;
  margin-bottom: 30px;
  text-align: center;
}

.label {
  display: block;
  text-align: left;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #222;
}

.submit-notify {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 40px;
  margin-bottom: 20px;
}

.error-message {
  color: #d9534f;
  background-color: #f2dede;
  border: 1px solid #ebccd1;
  padding: 15px;
  border-radius: 8px;
  text-align: center;
  margin-bottom: 20px;
}
</style>
