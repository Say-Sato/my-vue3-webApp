<script setup lang="ts">
import { ref, watch } from 'vue'
import { useUserStore } from '@/stores/user'

import TextInput from '@/basics/TextInput.vue'
import RadioInput from '@/basics/RadioInput.vue'
import IconButton from '@/basics/IconButton.vue'
import BirthDate from '@/basics/BirthDate.vue'
import PhotoDragDrop from '@/basics/PhotoDragDrop.vue'
import FormWrapper from '@/basics/FormWrapper.vue'

const props = defineProps<{
  userName?: string
  certificate18?: string
  profilePhoto?: File[]
  password?: string
  body?: string
  birthDate?: string
}>()

const emit = defineEmits<{
  (e: 'submit'): void
  (e: 'update:userName', value: string): void
  (e: 'update:certificate18', value: string): void
  (e: 'update:profilePhoto', value: File[]): void
  (e: 'update:password', value: string): void
  (e: 'update:body', value: string): void
  (e: 'update:birthDate', value: string): void
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
    emit('submit')
  } catch (err) {
    console.error('更新失敗:', err)
    errorMessage.value = '更新に失敗しました。もう一度お試しください。'
  }
}
</script>

<template>
  <FormWrapper>
    <form @submit.prevent="handleSubmit">
      <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>

      <div>
        <label for="image">プロフィール画像</label>
        <PhotoDragDrop v-model="localProfilePhoto" :maxCount="1" />
      </div>

      <div>
        <p>生年月日</p>
        <BirthDate v-model="localBirthDate" />
      </div>

      <div>
        <label for="userName">名前</label>
        <TextInput
          id="userName"
          name="userName"
          type="text"
          v-model="localUserName"
        />
      </div>

      <div>
        <label for="body">プロフィール本文</label>
        <TextInput id="body" name="body" type="textarea" v-model="localBody" />
      </div>

      <label for="password">パスワード</label>
      <TextInput
        id="password"
        name="password"
        type="password"
        v-model="localPassword"
      />

      <p>年齢制限ありの画像を表示する</p>
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
      <div class="submit-edit">
        <IconButton label="キャンセル" backgroundColor="#ccc" />
        <IconButton type="submit" label="保存する" />
      </div>
    </form>
  </FormWrapper>
</template>

<style scoped>
.wrapper {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.title {
  font-size: 24px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 20px;
}

.label {
  display: block;
  text-align: left;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #222;
}

.submit-edit {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 40px;
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
