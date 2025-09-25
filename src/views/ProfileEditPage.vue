<script setup lang="ts">
import { ref, watch } from 'vue'
import { useUserStore } from '@/stores/user'

import TextInput from '@/basics/TextInput.vue'
import RadioInput from '@/basics/RadioInput.vue'
import IconButton from '@/basics/IconButton.vue'
import BirthDate from '@/basics/BirthDate.vue'
import PhotoDragDrop from '@/basics/PhotoDragDrop.vue'

const props = defineProps<{
  userName?: string
  certificate18?: string
  profilePhoto?: File[]
  password?: string
  body?: string
  birthDate?: string
  editProfile: boolean
}>()

const emit = defineEmits<{
  (e: 'submit'): void
  (e: 'update:editProfile', value: boolean): void
}>()

// ローカル状態
const localUserName = ref('')
const localPassword = ref('')
const localCertificate18 = ref('0')
const localBody = ref('')
const localBirthDate = ref('')
const localProfilePhoto = ref<File[]>([])

// フラグ（編集判定）
const isEdited = ref({
  userName: false,
  password: false,
  body: false,
  birthDate: false,
  certificate18: false,
  profilePhoto: false,
})

// props が来たら local に代入
watch(
  () => props.userName,
  val => {
    if (val !== undefined) localUserName.value = val
  },
  { immediate: true }, // 初回も反映
)

watch(
  () => props.password,
  val => {
    if (val !== undefined) localPassword.value = val
  },
  { immediate: true },
)

watch(
  () => props.certificate18,
  val => {
    if (val != null || val === '0') {
      localCertificate18.value = String(val) // ← 明示的に文字列化
    }
  },
  { immediate: true },
)

watch(
  () => props.body,
  val => {
    if (val !== undefined) localBody.value = val
  },
  { immediate: true },
)

watch(
  () => props.birthDate,
  val => {
    if (val !== undefined) localBirthDate.value = val
  },
  { immediate: true },
)

// 変更を監視して編集フラグを立てる
watch(localUserName, v => (isEdited.value.userName = v !== props.userName))
watch(localPassword, v => (isEdited.value.password = v !== props.password))
watch(localBody, v => (isEdited.value.body = v !== props.body))
watch(localBirthDate, v => (isEdited.value.birthDate = v !== props.birthDate))
watch(
  localCertificate18,
  v => (isEdited.value.certificate18 = v !== props.certificate18),
)
watch(localProfilePhoto, v => (isEdited.value.profilePhoto = v.length > 0))

const errorMessage = ref('')
const userStore = useUserStore()
const id = ref(userStore.id)

// 送信処理
const handleSubmit = async () => {
  const formData = new FormData()

  if (isEdited.value.userName)
    formData.append('userName', localUserName.value || '')
  if (isEdited.value.password)
    formData.append('password', localPassword.value || '')
  if (isEdited.value.certificate18)
    formData.append('certificate18', localCertificate18.value || '0')
  if (isEdited.value.body) formData.append('body', localBody.value || '')
  if (isEdited.value.birthDate)
    formData.append('birthDate', localBirthDate.value || '')
  if (isEdited.value.profilePhoto && localProfilePhoto.value.length > 0) {
    formData.append('profilePhoto', localProfilePhoto.value[0])
  }

  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/ProfileEditAPI.php/${id.value}`,
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
    // ✅ userStore に渡す用のオブジェクトを作成
    userStore.editProfile({
      name:
        isEdited.value.userName && localUserName.value
          ? localUserName.value
          : userStore.name,

      certificate18:
        isEdited.value.certificate18 && localCertificate18.value !== undefined
          ? Number(localCertificate18.value)
          : userStore.certificate18,

      birthDate:
        isEdited.value.birthDate && localBirthDate.value
          ? localBirthDate.value
          : userStore.birthDate,
    })

    emit('submit')
    emit('update:editProfile', false)
  } catch (err) {
    console.error('更新失敗:', err)
    errorMessage.value = '更新に失敗しました。もう一度お試しください。'
  }
}

// キャンセルボタン処理
const closeForm = () => {
  emit('update:editProfile', false) // 親の editProfile を false にする
}
</script>

<template>
  <form @keydown.enter.prevent>
    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>

    <div class="profile-item">
      <label for="image">プロフィール画像</label>
      <PhotoDragDrop v-model="localProfilePhoto" :maxCount="1" />
    </div>

    <div class="profile-item">
      <label>生年月日</label>
      <BirthDate v-model="localBirthDate" />
    </div>

    <div class="profile-item">
      <label for="userName">名前</label>
      <TextInput
        id="userName"
        name="userName"
        type="text"
        v-model="localUserName"
      />
    </div>

    <div class="profile-item">
      <label for="body">プロフィール本文</label>
      <TextInput id="body" name="body" type="textarea" v-model="localBody" />
    </div>

    <div class="profile-item">
      <label for="password">パスワード</label>
      <TextInput
        id="password"
        name="password"
        type="password"
        v-model="localPassword"
      />
    </div>

    <div class="profile-item">
      <label>年齢制限ありの画像を表示する</label>
      <RadioInput
        id="show"
        name="certificate18"
        value="1"
        label="表示"
        v-model="localCertificate18"
      />
      <RadioInput
        id="hide"
        name="certificate18"
        value="0"
        label="非表示"
        v-model="localCertificate18"
      />
    </div>
    <div class="submit-edit">
      <IconButton
        label="キャンセル"
        backgroundColor="secondary"
        @click="closeForm"
        type="button"
      />
      <IconButton label="保存する" @click="handleSubmit" type="button" />
    </div>
  </form>
</template>

<style scoped>
.title {
  font-size: 24px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 20px;
}

.profile-item label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #555;
  margin-bottom: 8px;
}

.profile-item {
  margin-bottom: 20px;
}

.submit-edit {
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
