<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import IconButton from '@/basics/IconButton.vue'

// ストアとルーターを初期化
const userStore = useUserStore()
const router = useRouter()
const userId = ref(userStore.id)

// プロフィールデータを格納するリアクティブな変数
const userName = ref('')
const profileBody = ref('')
const birthDate = ref('')
const errorMessage = ref('')
const login_id = ref('')
const password = ref('')
const certificate18 = ref('')

// コンポーネントがマウントされたらAPIからプロフィール情報を取得
onMounted(async () => {
  if (!userId.value) {
    errorMessage.value = 'ユーザーIDが見つかりません。'
    return
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
    certificate18.value = data.certificate18 || '未設定'
  } catch (error) {
    console.error('プロフィールの取得エラー:', error)
    errorMessage.value = 'プロフィールの読み込み中にエラーが発生しました。'
  }
})

// 編集ページに遷移する関数
const goToEditPage = () => {
  router.push('/profile/edit')
}
</script>

<template>
  <div class="profile-container">
    <div class="profile-card">
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
          <p>{{ certificate18 ? '表示する' : '表示しない' }}</p>
        </div>
      </div>
      <div class="button-area">
        <IconButton label="編集する" @click="goToEditPage" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-container {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 40px 20px;
  min-height: 100vh;
  box-sizing: border-box;
}

.profile-card {
  background: #ffffff;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  width: 100%;
  max-width: 600px;
  text-align: left;
}

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
}
</style>
