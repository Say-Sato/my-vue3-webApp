<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useUserStore } from '@/stores/user'
import type { PropType } from 'vue'

import TextInput from '@/basics/TextInput.vue'
import RadioInput from '@/basics/RadioInput.vue'
import IconButton from '@/basics/IconButton.vue'
import BirthDate from '@/basics/BirthDate.vue'
import PhotoDragDrop from '@/basics/PhotoDragDrop.vue'
import FormWrapper from '@/basics/FormWrapper.vue'

const props = defineProps<{
  userName: string
  certificate18: string
  profilePhoto?: PropType<File[]>
  password: string
  body: string
  birthDate: string
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

// ローカルの状態を定義（フォーム入力用）
const localUserName = ref(props.userName ?? '')
const localPassword = ref(props.password ?? '')
const localCertificate18 = ref(props.certificate18 ?? '0')
const localBody = ref(props.body ?? '')
const localBirthDate = ref(props.birthDate ?? '')
const localProfilePhoto = ref<File[]>([])

const isEditedUserName = ref(false)
const isEditedPassword = ref(false)
const isEditedBirthDate = ref(false)
const isEditedBody = ref(false)
const isEditedCertificate18 = ref(true)

const errorMessage = ref('')
const userStore = useUserStore()
const id = ref(userStore.id)

// propsの変化を監視してローカル値を更新
watch(
  () => props.userName,
  val => (localUserName.value = val ?? ''),
)
watch(
  () => props.password,
  val => (localPassword.value = val ?? ''),
)
watch(
  () => props.certificate18,
  val => (localCertificate18.value = val ?? ''),
)
watch(
  () => props.body,
  val => (localBody.value = val ?? ''),
)
watch(
  () => props.birthDate,
  val => (localBirthDate.value = val ?? ''),
)

// フォームの送信処理
const handleSubmit = async () => {
  const formData = new FormData()

  const entries = [
    ['userName', isEditedUserName.value, localUserName.value],
    ['password', isEditedPassword.value, localPassword.value],
    ['certificate18', isEditedCertificate18.value, localCertificate18.value],
    ['body', isEditedBody.value, localBody.value],
    ['birthDate', isEditedBirthDate.value, localBirthDate.value],
  ] as const

  for (const [key, edited, value] of entries) {
    if (edited) formData.append(key, value)
  }

  if (localProfilePhoto.value.length > 0) {
    formData.append('profilePhoto', localProfilePhoto.value[0])
  }

  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/ProfileEditAPI.php/${id.value}`,
      {
        method: 'POST',
        body: formData,
      },
    )

    const contentType = response.headers.get('Content-Type') || ''
    if (!contentType.includes('application/json')) {
      throw new Error('サーバーからJSON形式の返答がありませんでした。')
    }

    const result = await response.json()

    if (!response.ok) {
      throw new Error(result.message || `HTTP ${response.status}`)
    }

    alert('データが正常に送信されました')
    emit('submit')
  } catch (error) {
    console.error('更新失敗:', error)
    errorMessage.value = '更新に失敗しました。もう一度お試しください。'
  }
}

// 初期データをサーバーから取得
onMounted(async () => {
  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/ProfileAllCatchAPI.php/${id.value}`,
    )
    const contentType = response.headers.get('Content-Type') || ''
    if (!contentType.includes('application/json'))
      throw new Error('JSONとして受け取れませんでした。')

    const data = await response.json()
    localUserName.value = data.name ?? ''
    localPassword.value = data.password ?? ''
    localBody.value = data.body ?? ''
    localBirthDate.value = data.birthDate ?? ''
    localCertificate18.value = String(data.certificate18 ?? '0')
  } catch (error) {
    console.error('初期データの取得に失敗:', error)
    errorMessage.value = '初期データの取得に失敗しました。'
  }
})
</script>

<template>
  <FormWrapper>
    <div class="wrapper">
      <form @submit.prevent="handleSubmit">
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>

        <div>
          <label for="image">プロフィール画像</label>
          <PhotoDragDrop v-model="localProfilePhoto" :maxCount="1" />
        </div>

        <div>
          <p>生年月日</p>
          <BirthDate v-model="localBirthDate" />
        </div>

        <div>
          <br /><label for="userName">名前</label>
          <TextInput
            id="userName"
            class="userName"
            name="userName"
            type="text"
            v-model="localUserName"
          />
        </div>
        <div>
          <br /><label for="body">プロフィール本文</label>
          <TextInput
            id="body"
            class="body"
            name="body"
            type="textarea"
            v-model="localBody"
          />
        </div>

        <br /><label for="password">パスワード</label>
        <TextInput
          id="password"
          class="password"
          name="password"
          type="password"
          v-model="localPassword"
        />

        <br />
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
    </div>
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
