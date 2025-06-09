<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'InputComponent',
  props: {
    id: {
      type: String,
      default: '',
    },
    className: {
      type: String,
      default: '',
    },
    name: {
      type: String,
      default: '',
    },
    type: {
      type: String,
      default: 'text',
    },
    // v-model 用のプロパティ
    modelValue: {
      type: String,
      default: '',
    },
  },
  methods: {
    handleInput(event: Event) {
      // 値が存在するかどうかを確認し、null なら空文字に置き換え
      const value = (event.target as HTMLInputElement).value ?? ''
      this.$emit('update:modelValue', value)
    },
  },
})
</script>

<template>
  <div class="textInput">
    <!-- typeが'textarea'の場合はtextarea、それ以外はinputを表示 -->
    <textarea
      v-if="type === 'textarea'"
      :id="id"
      :class="className"
      :name="name"
      v-bind="$attrs"
      :value="modelValue"
      @input="handleInput"
    ></textarea>
    <input
      v-else
      :type="type"
      :id="id"
      :class="className"
      :name="name"
      v-bind="$attrs"
      :value="modelValue"
      @input="handleInput"
    />
  </div>
</template>

<style scoped>
/* inputおよびtextareaの共通スタイル */
.textInput input[type='text'],
.textInput input[type='password'],
.textInput textarea {
  margin-right: 5px; /* ボタンとの間隔をあける */
  width: 190px;
  height: 32px;
  padding: 1px 12px; /* 内側の余白 */
  border-radius: 20px; /* 角を丸くする */
}

/* textarea固有のスタイル */
.textInput textarea {
  height: 100px; /* テキストエリアの高さを設定 */
  resize: vertical; /* ユーザーがサイズを縦方向に変更できるようにする */
  border-width: 2px;
}
</style>
