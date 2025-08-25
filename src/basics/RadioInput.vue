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
  <div class="radio-item">
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
  </div>
</template>

<style scoped>
.radio-item {
  /* 親要素がflexコンテナの場合、横に並ぶようになります */
  display: inline-flex;
  align-items: center;
  /* 各ラジオボタンの右側に間隔を設けます */
  margin-right: 20px;
  margin-bottom: 10px; /* 縦に並んだ場合の間隔 */
}

/* 最後の要素の右側のマージンをなくし、不要な余白を防ぎます */
.radio-item:last-child {
  margin-right: 0;
}

input[type='radio'] {
  margin-right: 15px; /* ラジオボタンとラベルの間のスペース */
}

label {
  font-size: 14px;
  cursor: pointer;
  /* ユーザーがテキストを選択できないようにして、クリック操作を改善 */
  user-select: none;
}
</style>
