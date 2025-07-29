<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
const email = ref('')
const router = useRouter()

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
  if (result.success) {
    alert('確認メールを送信したわよ。ちゃんと受け取りなさい！')
    router.push('/register/complete')
  } else {
    alert('メール送信に失敗したわよ。もう一回試しなさい！')
  }
}
</script>

<template>
  <div class="register-container">
    <div class="register-card">
      <p class="description">
        仮登録のため、メールアドレスをご入力ください。<br />
        ご入力いただいたアドレス宛に本登録用のリンクをお送りします。
      </p>
      <label class="label" for="email">メールアドレス</label>
      <input
        id="email"
        type="email"
        v-model="email"
        class="input"
        placeholder="your@example.com"
      />
      <button class="submit-button" @click="sendPreRegister">送信</button>
    </div>
  </div>
</template>

<style scoped>
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  background-color: #fff;
}

.register-card {
  background-color: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  padding: 40px 30px;
  width: 100%;
  max-width: 400px;
  text-align: center;
}

.description {
  font-size: 14px;
  color: #333;
  margin-bottom: 24px;
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

.input {
  width: 100%;
  padding: 10px 14px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 20px;
  outline: none;
  margin-bottom: 20px;
}

.submit-button {
  background-color: #000;
  color: #fff;
  font-size: 14px;
  padding: 10px 24px;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  transition: 0.3s;
}

.submit-button:hover {
  opacity: 0.85;
}
</style>
