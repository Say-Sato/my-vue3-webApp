<script setup lang="ts">
import FormWrapper from '@/basics/FormWrapper.vue'
import IconButton from '@/basics/IconButton.vue'

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
  <FormWrapper>
    <div class="message-card">
      <h2 class="message-title">{{ title }}</h2>
      <p class="message-text">{{ msg }}</p>
      <div class="message-button">
        <IconButton :label="buttonText" @click="onClick" />
      </div>
    </div>
  </FormWrapper>
</template>

<style scoped>
.message-card {
  width: 100%;
  text-align: center;
}

.message-title {
  font-size: 1.5rem;
  margin-bottom: 2rem;
}

.message-text {
  font-size: 1rem;
  color: #555;
  margin-bottom: 3rem;
  line-height: 1.6;
  white-space: pre-line;
  text-align: justify;
}
.message-button {
  padding-bottom: 20px;
}
</style>
