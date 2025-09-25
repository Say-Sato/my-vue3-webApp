<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
//import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import IconButton from '@/basics/IconButton.vue'
import FormWrapper from '@/basics/FormWrapper.vue'
import ProfileEditPage from '@/views/ProfileEditPage.vue'
import NotifyEditPage from '@/pages/ProfileNotifySet.vue'

type notify = {
  comment: string
  follow: string
  favorite: string
  illust: string
}

// ストアとルーターを初期化
const userStore = useUserStore()
//const router = useRouter()
const userId = ref(userStore.id)

// プロフィールデータを格納するリアクティブな変数
const userName = ref('')
const profileBody = ref('')
const birthDate = ref('')
const errorMessage = ref('')
const login_id = ref('')
const password = ref('')
const certificate18 = ref('')
const editProfile = ref(false)
const editNotify = ref(false)
const commentNotification = ref('')
const followNotification = ref('')
const favoriteNotification = ref('')
const illustNotification = ref('')
const customNotification = ref(false)
const notify = ref<notify[]>([])

// APIからプロフィール情報を取得
const fetchData = async () => {
  if (!userId.value) {
    errorMessage.value = 'ユーザーIDが見つかりません。'
    return
  }

  if (editProfile.value === true) {
    editProfile.value = false
  }

  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/ProfileAllCatchAPI.php/${userId.value}`,
    )
    if (!response.ok) {
      throw new Error('プロフィールの取得に失敗しました。')
    }
    const data = await response.json()
    userName.value = data.name || '未設定'
    profileBody.value = data.body || '未設定'
    birthDate.value = data.birthDate || '未設定'
    login_id.value = data.login_id || '未設定'
    password.value = data.password || '未設定'
    certificate18.value = String(data.certificate18) || '未設定'
    commentNotification.value = String(data.n_comment) || '未設定'
    followNotification.value = String(data.n_follow) || '未設定'
    favoriteNotification.value = String(data.n_favorite) || '未設定'
    illustNotification.value = String(data.n_illust) || '未設定'

    notify.value.push({
      comment: commentNotification.value,
      follow: followNotification.value,
      favorite: favoriteNotification.value,
      illust: illustNotification.value,
    })
  } catch (error) {
    console.error('プロフィールの取得エラー:', error)
    errorMessage.value = 'プロフィールの読み込み中にエラーが発生しました。'
  }
}

watch(editProfile, newVal => {
  if (newVal === false) {
    fetchData()
  }
})

watch(editNotify, newVal => {
  if (newVal === false) {
    fetchData()
  }
})

watch(
  [
    commentNotification,
    followNotification,
    favoriteNotification,
    illustNotification,
  ],
  ([c, f, fav, i]) => {
    // customNotification は真偽値
    customNotification.value =
      c !== '1' || f !== '1' || fav !== '1' || i !== '1'

    // notify は最新の状態だけをセット
    notify.value = [
      {
        comment: c,
        follow: f,
        favorite: fav,
        illust: i,
      },
    ]
  },
  { immediate: true },
)

onMounted(fetchData)

// 編集ページに遷移する関数
const goToEditPage = () => {
  //router.push('/profile/edit')
  editProfile.value = true
}

// 通知編集ページに遷移する関数
const goToNotifyPage = () => {
  //router.push('/profile/edit')
  editNotify.value = true
}
</script>

<template>
  <FormWrapper>
    <span v-if="!editProfile && !editNotify">
      <h1 class="profile-title">Profile</h1>
      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </div>
      <div v-else class="profile-content">
        <div class="profile-item">
          <label>ユーザー名</label>
          <p>{{ userName }}</p>
        </div>
        <div class="profile-item">
          <label>自己紹介</label>
          <p class="profile-body">{{ profileBody }}</p>
        </div>
        <div class="profile-item">
          <label>生年月日</label>
          <p>{{ birthDate }}</p>
        </div>
        <div class="profile-item">
          <label>ログインID</label>
          <p>{{ login_id }}</p>
        </div>
        <div class="profile-item">
          <label>パスワード</label>
          <p>{{ password ? '●●●●●●' : '未設定' }}</p>
        </div>
        <div class="profile-item">
          <label>年齢制限付きの画像</label>
          <p>{{ Number(certificate18) ? '警告する' : '表示する' }}</p>
        </div>
        <div class="button-area">
          <IconButton label="編集する" @click="goToEditPage" />
        </div>
        <div class="profile-item">
          <label>通知</label>
          <p>{{ customNotification ? 'カスタム' : 'デフォルト' }}</p>
        </div>
      </div>
      <div class="button-area">
        <IconButton label="編集する" @click="goToNotifyPage" />
      </div>
    </span>
    <span v-else-if="editProfile">
      <ProfileEditPage
        :userName="userName"
        :certificate18="certificate18"
        :password="password"
        :body="profileBody"
        :birthDate="birthDate"
        v-model:editProfile="editProfile"
    /></span>
    <span v-else
      ><NotifyEditPage v-model:editNotify="editNotify" :notification="notify"
    /></span>
  </FormWrapper>
</template>

<style scoped>
.profile-title {
  font-size: 28px;
  font-weight: 700;
  color: #333;
  margin-bottom: 30px;
  text-align: center;
}

.profile-content {
  margin-bottom: 30px;
}

.profile-item {
  margin-bottom: 20px;
}

.profile-item label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #555;
  margin-bottom: 8px;
}

.profile-item p {
  font-size: 16px;
  color: #333;
  margin: 0;
  padding: 10px;
  background-color: #f9f9f9;
  border-radius: 6px;
  border: 1px solid #eee;
}

.profile-item .profile-body {
  white-space: pre-wrap; /* 改行を反映させる */
  line-height: 1.6;
}

.edit-button {
  display: block;
  width: 100%;
  padding: 12px;
  font-size: 16px;
  font-weight: 600;
  color: #fff;
  background-color: #007bff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.edit-button:hover {
  background-color: #0056b3;
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

.button-area {
  display: flex;
  justify-content: flex-end; /* 右寄せ */
  margin-top: 40px;
  margin-bottom: 20px;
}
</style>
