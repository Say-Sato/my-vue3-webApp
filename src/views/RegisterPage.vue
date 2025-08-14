<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const token = route.query.token as string
const isValid = ref<boolean | null>(null)

onMounted(async () => {
  console.log('取得したトークン:', token)

  if (!token) {
    router.push('/invalid-token')
    return
  }

  try {
    const res = await fetch(
      'https://yellowokapi2.sakura.ne.jp/Vue/api/VerifyTokenAPI.php',
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token }),
      },
    )

    const data = await res.json()

    if (data.valid) {
      isValid.value = true
    } else {
      router.push('/invalid-token')
    }
  } catch (err) {
    console.error('API通信エラー:', err)
    router.push('/invalid-token')
  }
})

const loginId = ref('')
const password = ref('')
const isPasswordVisible = ref(false)

function togglePassword() {
  isPasswordVisible.value = !isPasswordVisible.value
}

const submitForm = async () => {
  console.log('登録:', { loginId: loginId.value, password: password.value })

  try {
    const response = await fetch(
      'https://yellowokapi2.sakura.ne.jp/Vue/api/RegisterAPI.php',
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          loginId: loginId.value,
          password: password.value,
        }),
      },
    )

    const result = await response.json()
    console.log('APIレスポンス:', result)

    if (result.success) {
      alert('登録完了!')
      router.push('/register/complete') // 任意でリダイレクト
    } else {
      alert(result.message || '登録失敗よ！もっとちゃんと入力しなさい！')
    }
  } catch (err) {
    console.error('登録API通信エラー:', err)
    alert('通信エラーよ！あとでやり直しなさい！')
  }
}
</script>

<template>
  <div v-if="isValid === null">
    <p>トークン確認中…</p>
  </div>
  <div v-else-if="isValid">
    <div class="register-container">
      <div class="form-card">
        <h1 class="form-title">ようこそ！</h1>
        <p class="form-description">
          メール確認ありがとうございます！<br />
          続けて、ログイン用のIDとパスワードを設定してください。<br />
          この設定が終わると、登録が完了します。
        </p>

        <form @submit.prevent="submitForm">
          <label class="form-label">ログインID</label>
          <input v-model="loginId" type="text" class="form-input" />

          <label class="form-label">パスワード</label>
          <div class="password-wrapper">
            <input
              v-model="password"
              :type="isPasswordVisible ? 'text' : 'password'"
              class="form-input"
            />
            <button type="button" class="eye-icon" @click="togglePassword">
              <span v-if="isPasswordVisible">🙈</span>
              <span v-else>👁️</span>
            </button>
          </div>

          <button type="submit" class="submit-button">登録</button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: #fff;
}

.form-card {
  background: #fff;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  width: 320px;
}

.form-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 1rem;
  text-align: center;
}

.form-description {
  font-size: 0.9rem;
  color: #333;
  margin-bottom: 1.5rem;
  line-height: 1.6;
  text-align: center;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 0.85rem;
  color: #222;
}

.form-input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  margin-bottom: 1rem;
  border: 2px solid #000;
  border-radius: 9999px;
  outline: none;
  font-size: 1rem;
}

.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.eye-icon {
  position: absolute;
  right: 0.75rem;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
}

.submit-button {
  display: block;
  width: 100%;
  background: #111;
  color: #fff;
  padding: 0.5rem;
  font-size: 1rem;
  border-radius: 9999px;
  border: none;
  cursor: pointer;
  margin-top: 0.5rem;
}
</style>
