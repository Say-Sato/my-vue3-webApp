<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router' // Vue Routerをインポート
import { useUserStore } from '@/stores/user'
import TextInput from '@/basics/TextInput.vue'

// ログイン情報を保持する状態
const loginid = ref('')
const password = ref('')
const errorMessage = ref('') // エラーメッセージを格納する状態
const userData = ref(null) // JSONデータ全体を格納
const router = useRouter() // ルーターインスタンスの作成
const userStore = useUserStore()

// データ取得関数
const fetchData = async () => {
  try {
    const response = await fetch(
      'https://yellowokapi2.sakura.ne.jp/Vue/api/LoginAPI.php',
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          loginid: loginid.value,
          password: password.value,
        }),
      },
    )

    const data = await response.json()
    userData.value = data // JSONデータを状態に保存

    if (!response.ok) {
      // エラーメッセージを設定
      errorMessage.value = data.error || 'Unknown error occurred'
      throw new Error(errorMessage.value)
    }

    if (data.id) {
      // ログイン情報が正しいかつ既にログインしている場合
      if (userStore.id !== '') {
        // ログイン情報を削除
        userStore.logout()
      }

      // ログイン情報を localStorage に保存
      userStore.login(data.id, data.name, data.admin, data.login_id)
      console.log('ユニークID:', userStore.id)
      console.log('ユーザー名:', userStore.login_id)
      console.log('ユーザー名:', userStore.name)
      console.log('管理者フラグ:', userStore.admin)
      console.log('ログイン状態:', userStore.isLogin)
      console.log(data.login_id)
      router.push({ path: `/home/${data.id}` })
    } else {
      errorMessage.value = 'IDもしくはパスワードが間違っています。'
    }
  } catch (error) {
    if (error instanceof Error) {
      console.error('Error:', error.message)
    } else {
      console.error('An unknown error occurred')
    }
  }
}

// フォーム送信時の処理
const handleSubmit = () => {
  fetchData() // フォームが送信されたら fetchData を実行
}
</script>

<template>
  <div class="container">
    <h2>ログイン</h2>

    <form @submit.prevent="handleSubmit">
      <div class="login">
        <label for="loginid">ログインID : </label>
        <TextInput
          id="loginid"
          className="loginid"
          name="loginid"
          v-model="loginid"
          required
        /><br />
        <label for="password">パスワード : </label>
        <TextInput
          id="password"
          className="password"
          name="password"
          type="password"
          v-model="password"
          required
        />
      </div>
      <button type="submit">ログイン</button>
    </form>
    <router-link class="register" to="/register/temporary"
      >新規登録</router-link
    >
    <div v-if="errorMessage" class="error">
      <p>{{ errorMessage }}</p>
    </div>
  </div>
</template>

<style scoped>
.title {
  margin: 12px;
  font-size: 20px;
  font-weight: bold;
  text-align: center;
}

.dtl {
  line-height: 1.5;
}

.image-container {
  width: 95%; /* 画面全体の80%の幅 */
  height: 600px; /* 高さは固定（必要に応じて変更可） */
  display: block; /* ブロック要素として設定 */
  overflow: hidden; /* 画像が領域を超えた場合に隠す */
  background-color: #f0f0f0; /* 背景色を指定（オプション） */
  margin: 0 auto; /* 左右のマージンを自動で設定（中央寄せ） */
}

.image {
  width: 100%;
  height: 100%;
  object-fit: contain; /* 領域内に収める */
  object-position: center; /* 中央寄せにする */
}

.error {
  color: red;
}

.container {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

form {
  background: white;
  padding: 40px 30px;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  min-width: 320px;
  max-width: 380px;
  width: 100%;
}

.login {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 24px;
}

label {
  font-size: 14px;
  margin-bottom: 4px;
}

input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #999;
  border-radius: 20px;
  font-size: 14px;
}

button[type='submit'] {
  width: 100%;
  padding: 10px;
  background: #000;
  color: white;
  font-weight: bold;
  border-radius: 20px;
  border: none;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  transition: background 0.2s;
}

button[type='submit']:hover {
  background: #222;
}

.register {
  font-size: 12px;
  color: #1976d2;
  text-decoration: none;
  display: inline-block;
  margin-top: 8px;
}
</style>
