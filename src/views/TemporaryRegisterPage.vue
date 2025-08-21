<script setup lang="ts">
import { ref } from 'vue'

import TextInput from '@/basics/TextInput.vue'
import IconButton from '@/basics/IconButton.vue'
//import { useRouter } from 'vue-router'
const email = ref('')
//const router = useRouter()

const sendPreRegister = async () => {
  const response = await fetch(
    'https://yellowokapi2.sakura.ne.jp/Vue/api/TemporaryRegisterMailAPI.php',
    {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value }),
    },
  )

  const result = await response.json()
  console.log('APIレスポンス:', result)

  if (result.success) {
    alert('確認メールを送信したわよ。ちゃんと受け取りなさい！')
    //router.push('/register/complete')
  } else {
    alert('メール送信に失敗したわよ。もう一回試しなさい！')
  }
}
</script>

<template>
  <div class="container">
    <div class="register-card">
      <div class="wrapper">
        <p class="description">
          仮登録のため、メールアドレスをご入力ください。<br />
          ご入力いただいたアドレス宛に本登録用のリンクをお送りします。
        </p>
        <label class="label" for="email">メールアドレス</label>
        <TextInput
          id="email"
          className="email"
          name="email"
          v-model="email"
          required
        /><br />
        <div class="submit-email">
          <IconButton
            label="送信"
            @click="sendPreRegister"
            class="submit-button"
          />
        </div>
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

.submit-email {
  display: flex;
  justify-content: flex-end; /* 右寄せ */

  margin-bottom: -20px;
}
</style>
