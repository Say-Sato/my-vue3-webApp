<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

import TextInput from '@/basics/TextInput.vue'
import IconButton from '@/basics/IconButton.vue'

const router = useRouter()
const route = useRoute()

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
  <div class="container" v-else-if="isValid">
    <div class="register-card">
      <div class="wrapper">
        <h2 class="title">ようこそ！</h2>
        <p class="description">
          メール確認ありがとうございます！<br />
          続けて、ログイン用のIDとパスワードを設定してください。<br />
          この設定が終わると、登録が完了します。
        </p>

        <form @submit.prevent="submitForm">
          <label for="loginid">ログインID</label>
          <TextInput
            id="loginid"
            className="loginid"
            name="loginid"
            v-model="loginId"
            required
          /><br />
          <label for="password">パスワード</label>
          <TextInput
            id="password"
            className="password"
            name="password"
            type="password"
            v-model="password"
            required
          /><br />
          <div class="submit-register">
            <IconButton label="登録" type="submit" class="submit-button" />
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.container {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  padding-top: 20px;
}

.register-card {
  background: white;
  padding: 40px 30px;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  min-width: 320px;
  max-width: 380px;
  width: 100%;
}

.wrapper {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 30px;
}

.description {
  font-size: 14px;
  color: #333;
  margin-bottom: 30px;
  line-height: 1.5;
}

.label {
  display: block;
  text-align: left;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #222;
}

.wrapper h2 {
  text-align: center;
  margin-top: 0; /* 上の余白を消す */
  margin-bottom: 0; /* 下の余白をちょっとだけにする */
  padding: 0; /* 念のため */
}

.submit-register {
  display: flex;
  justify-content: flex-end; /* 右寄せ */
  padding-top: 20px;
  margin-bottom: -20px;
}
</style>
