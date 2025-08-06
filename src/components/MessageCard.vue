<script lang="ts" setup>
interface Props {
  title: string
  msg: string
  buttonText: string
  onClick: () => void
}

const props = defineProps<Props>()
const { title, msg, buttonText, onClick } = props
</script>

<template>
  <div class="message-wrapper">
    <div class="message-card">
      <h2 class="message-title">{{ title }}</h2>
      <p class="message-text">{{ msg }}</p>
      <button class="message-button" @click="onClick">
        {{ buttonText }}
      </button>
    </div>
  </div>
</template>

<style scoped>
.message-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 3rem 1rem;
}

.message-card {
  width: 100%;
  max-width: 600px;
  background-color: white;
  border-radius: 1rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  padding: 3rem 2rem;
  text-align: center;
}

.message-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 2rem;
}

.message-text {
  font-size: 1rem;
  color: #555;
  margin-bottom: 3rem;
  line-height: 1.6;
  white-space: pre-line;
}

.message-button {
  background-color: #ccc;
  border: none;
  border-radius: 9999px;
  padding: 0.5rem 2rem;
  font-size: 1rem;
  color: white;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.message-button:hover {
  background-color: #999;
}
</style>
