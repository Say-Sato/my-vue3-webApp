<script setup lang="ts">
const props = defineProps<{
  id?: string
  className?: string
  name?: string
  value?: string
  modelValue?: string
  label?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const handleInput = (event: Event) => {
  const value = (event.target as HTMLInputElement).value ?? ''
  emit('update:modelValue', value)
}
</script>

<template>
  <!-- ラジオボタンの生成 -->
  <input
    type="radio"
    :id="props.id"
    :class="props.className"
    :name="props.name"
    :value="props.value"
    :checked="props.modelValue === props.value"
    @change="handleInput"
  />
  <label :for="props.id">{{ props.label }}</label>
</template>

<style scoped>
input[type='radio'] {
  margin-right: 5px;
}
label {
  font-size: 14px;
  cursor: pointer;
}
</style>
