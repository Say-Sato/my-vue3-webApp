<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router' // Vue Routerをインポート
import { useUserStore } from '@/stores/user'
import TextInput from '@/basics/TextInput.vue'
import IconButton from '@/basics/IconButton.vue'

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
      userStore.login(
        data.id,
        data.name,
        data.admin,
        data.login_id,
        '1970-01-01 00:00:00',
      )
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
    <form @submit.prevent="handleSubmit">
      <div class="login">
        <br />
        <label for="loginid">ログインID</label>
        <TextInput
          id="loginid"
          className="loginid"
          name="loginid"
          v-model="loginid"
          required
        />
        <label for="password">パスワード</label>
        <TextInput
          id="password"
          className="password"
          name="password"
          type="password"
          v-model="password"
          required
        /><br />
      </div>
      <div class="submit-login">
        <router-link class="register" to="/register/temporary"
          >新規登録</router-link
        >
        <IconButton label="ログイン" type="submit" />
      </div>
    </form>
    <div v-if="errorMessage" class="error">
      <p>{{ errorMessage }}</p>
    </div>
  </div>
</template>

<style scoped>
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

.submit-login {
  display: flex;
  justify-content: space-between; /* 左右に配置 */
  align-items: center; /* 高さ揃え */
  margin-top: 40px;
}

.register {
  font-size: 12px;
  color: #1976d2;
  text-decoration: none;
  display: inline-block;
  margin-top: 8px;
}
</style>
