<script lang="ts">
import { defineComponent, ref, watch } from 'vue'
import TextInput from '@/basics/TextInput.vue'
import RadioInput from '@/basics/RadioInput.vue'
import BirthDate from '@/basics/BirthDate.vue'
import PhotoDragDrop from '@/basics/PhotoDragDrop.vue'

export default defineComponent({
  components: {
    TextInput,
    RadioInput,
    BirthDate,
    PhotoDragDrop,
  },
  props: {
    userName: String,
    certificate18: String,
    profilePhoto: {
      type: File,
      required: false,
    },

    password: String,
    body: String,
    birthDate: String,
  },
  emits: [
    'submit',
    'update:userName',
    'update:certificate18',
    'update:profilePhoto',
    'update:password',
    'update:body',
    'update:birthDate',
  ],
  setup(props, { emit }) {
    const localUserName = ref(props.userName ?? '')
    const localPassword = ref(props.password ?? '')
    const localCertificate18 = ref(props.certificate18 ?? '0')
    const localBody = ref(props.body ?? '')
    const localBirthDate = ref(props.birthDate ?? '')
    const localProfilePhoto = ref<File | null>(null)

    // propsが変わった時にローカル反映
    watch(
      () => props.userName,
      val => {
        localUserName.value = val ?? ''
      },
    )
    watch(
      () => props.password,
      val => {
        localPassword.value = val ?? ''
      },
    )
    watch(
      () => props.certificate18,
      val => {
        localCertificate18.value = val ?? ''
      },
    )
    watch(
      () => props.body,
      val => {
        localBody.value = val ?? ''
      },
    )
    watch(
      () => props.birthDate,
      val => {
        localBirthDate.value = val ?? ''
      },
    )
    watch(
      () => props.profilePhoto,
      val => {
        localProfilePhoto.value = val ?? null
      },
    )

    const errorMessage = ref<string>('')
    const id = ref('4')

    const isEditingUserName = ref(false)
    const isEditedUserName = ref(false)
    const isEditingCertificate18 = ref(false)
    const isEditedCertificate18 = ref(true)
    const isEditingBirthDate = ref(false)
    const isEditedBirthDate = ref(false)
    const isEditingPassword = ref(false)
    const isEditedPassword = ref(false)
    const isEditingBody = ref(false)
    const isEditedBody = ref(false)
    const passwordHide = ref(true)

    const toggleEdit = (field: string) => {
      switch (field) {
        case 'userName':
          isEditingUserName.value = !isEditingUserName.value
          isEditedUserName.value = true
          break
        case 'password':
          isEditingPassword.value = !isEditingPassword.value
          isEditedPassword.value = true
          if (!isEditingPassword.value) {
            passwordHide.value = true
          }
          break
        case 'birthDate':
          isEditingBirthDate.value = !isEditingBirthDate.value
          isEditedBirthDate.value = true
          break
        case 'body':
          isEditingBody.value = !isEditingBody.value
          isEditedBody.value = true
          break
      }
    }

    const showPassword = () => {
      passwordHide.value = !passwordHide.value
    }

    const handleSubmit = async () => {
      const formData = new FormData()

      const entries = [
        ['userName', isEditedUserName.value, localUserName.value],
        ['password', isEditedPassword.value, localPassword.value],
        [
          'certificate18',
          isEditedCertificate18.value,
          localCertificate18.value,
        ],
        ['body', isEditedBody.value, localBody.value],
        ['birthDate', isEditedBirthDate.value, localBirthDate.value],
      ] as const

      for (const [key, edited, value] of entries) {
        if (edited) formData.append(key, value)
      }

      if (localProfilePhoto.value instanceof File) {
        formData.append('profilePhoto', localProfilePhoto.value)
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

        console.log('送信成功:', result)
        alert('データが正常に送信されました')
        emit('submit')
      } catch (error) {
        console.error('更新失敗:', error)
        errorMessage.value = '更新に失敗しました。もう一度お試しください。'
      }
    }

    return {
      localUserName,
      localPassword,
      localCertificate18,
      localBody,
      localBirthDate,
      localProfilePhoto,
      errorMessage,
      isEditingUserName,
      isEditingPassword,
      isEditingCertificate18,
      isEditingBirthDate,
      isEditingBody,
      passwordHide,
      toggleEdit,
      showPassword,
      handleSubmit,
    }
  },
})
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>

    <div>
      <PhotoDragDrop
        v-model="localProfilePhoto"
        labelBeforeText="プロフィール画像編集："
        labelAfterText="新規プロフィール画像："
      />
    </div>

    <!-- 生年月日 -->
    <div>
      <h2>選択された生年月日: {{ localBirthDate }}</h2>
      <template v-if="isEditingBirthDate">
        <BirthDate v-model="localBirthDate" />
      </template>
      <button type="button" @click="toggleEdit('birthDate')">
        {{ isEditingBirthDate ? '完了' : '編集' }}
      </button>
    </div>

    <!-- 名前 -->
    <div>
      <label for="userName">名前</label>
      <template v-if="isEditingUserName">
        <TextInput
          id="userName"
          class="userName"
          name="userName"
          type="text"
          v-model="localUserName"
        />
      </template>
      <template v-else>
        <br /><span>{{ localUserName }}</span>
      </template>
      <button type="button" @click="toggleEdit('userName')">
        {{ isEditingUserName ? '完了' : '編集' }}
      </button>
    </div>

    <div>
      <label for="body">プロフィール本文</label>
      <template v-if="isEditingBody">
        <TextInput
          id="body"
          class="body"
          name="body"
          type="textarea"
          v-model="localBody"
        />
      </template>
      <template v-else>
        <br /><span>{{ localBody }}</span>
      </template>
      <button type="button" @click="toggleEdit('body')">
        {{ isEditingBody ? '完了' : '編集' }}
      </button>
    </div>

    <!-- パスワード -->
    <div>
      <label for="password">パスワード</label>

      <!-- 編集中で表示状態：パスワード入力欄 -->
      <template v-if="isEditingPassword">
        <TextInput
          id="password"
          class="password"
          name="password"
          type="password"
          v-model="localPassword"
        />
      </template>

      <!-- 編集中で伏せ字状態：●●●と表示＋表示ボタン -->
      <template v-else-if="passwordHide">
        <br />
        <span>●●●●●●</span>
        <br />
        <button type="button" @click="showPassword">表示</button>
      </template>

      <!-- 編集していない：通常表示 -->
      <template v-else>
        <br />
        <span>{{ localPassword }}</span>
        <br />
        <button type="button" @click="showPassword">非表示</button>
      </template>

      <button type="button" @click="toggleEdit('password')">
        {{ isEditingPassword ? '完了' : '編集' }}
      </button>
    </div>

    <!-- 年齢制限表示設定 -->
    <p>年齢制限ありの画像を表示する</p>
    <div class="radio-buttons">
      <span class="radio">
        <RadioInput
          id="show"
          name="certificate18"
          value="1"
          label="表示"
          v-model="localCertificate18"
          required
        />
      </span>
      <span class="radio">
        <RadioInput
          id="hide"
          name="certificate18"
          value="0"
          label="非表示"
          v-model="localCertificate18"
          required
        />
      </span>
    </div>

    <button type="submit">保存する</button>
  </form>
</template>

<style scoped>
ul {
  list-style: none;
  padding: 0;
}
li {
  display: flex;
  align-items: center;
  gap: 5px;
}
.birthday-select select {
  appearance: none;
  background-color: white;
  color: black;
  border: 1px solid #aaa;
  border-radius: 6px;
  padding: 6px 12px;
  margin: 0 4px 8px 0;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}
.birthday-select select:focus {
  border-color: black;
  outline: none;
  box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.2);
}
.birthday-select {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
</style>
