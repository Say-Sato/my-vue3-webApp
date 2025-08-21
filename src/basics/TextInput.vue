<script lang="ts">
import { defineComponent, ref, computed } from 'vue'

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
    text: {
      type: String,
      default: '',
    },
    modelValue: {
      type: String,
      default: '',
    },
  },
  setup(props, { emit }) {
    const showPassword = ref(false)

    const currentType = computed(() => {
      if (props.type === 'password') {
        return showPassword.value ? 'text' : 'password'
      }
      return props.type
    })

    const handleInput = (event: Event) => {
      const value = (event.target as HTMLInputElement).value ?? ''
      emit('update:modelValue', value)
    }

    const togglePasswordVisibility = () => {
      showPassword.value = !showPassword.value
    }

    return {
      showPassword,
      currentType,
      handleInput,
      togglePasswordVisibility,
    }
  },
})
</script>

<template>
  <head>
    <link
      href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
      rel="stylesheet"
    />
  </head>

  <div class="textInput">
    <!-- textarea -->
    <textarea
      v-if="type === 'textarea'"
      :id="id"
      :class="className"
      :name="name"
      v-bind="$attrs"
      :value="modelValue"
      :placeholder="text"
      @input="handleInput"
    ></textarea>

    <!-- password -->
    <template v-else-if="type === 'password'">
      <div class="passwordWrapper">
        <input
          :type="currentType"
          :id="id"
          :class="className"
          :name="name"
          v-bind="$attrs"
          :value="modelValue"
          :placeholder="text"
          @input="handleInput"
        />

        <!-- 👁️ 表示時：斜線アイコン（非表示に切り替える） -->
        <label
          v-if="showPassword"
          for="checkPassword"
          class="togglePassword fa fa-eye-slash"
          @click.prevent="togglePasswordVisibility"
        ></label>

        <!-- 👁️ 非表示時：目アイコン（表示に切り替える） -->
        <label
          v-else
          for="checkPassword"
          class="togglePassword fa fa-eye"
          @click.prevent="togglePasswordVisibility"
        ></label>
      </div>
    </template>

    <!-- その他の input -->
    <template v-else>
      <input
        :type="currentType"
        :id="id"
        :class="className"
        :name="name"
        v-bind="$attrs"
        :value="modelValue"
        :placeholder="text"
        @input="handleInput"
      />
    </template>
  </div>
</template>

<style scoped>
.textInput input[type='text'],
.textInput input[type='password'],
.textInput textarea {
  width: 100%; /* 幅は内容に合わせる */
  max-width: 100%; /* 親の幅を超えないように制限 */
  margin-right: 5px;
  height: 32px;
  padding: 1px 12px;
  border-radius: 20px;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

/* textarea用 */
.textInput textarea {
  resize: vertical;
  width: 100%; /* 幅は内容に合わせる */
  max-width: 100%; /* 親の幅を超えないように制限 */
  height: 100px; /* 高さは固定か必要に応じて */
  min-width: 100px; /* 最小幅も適当に設定するといい */
  padding: 12px;
  border-width: 2px;
  box-sizing: border-box;
  display: inline-block; /* 干渉を減らすためにインラインブロックに */
}

/* パスワード入力用ラッパー */
.passwordWrapper {
  position: relative;
  width: 100%; /* 幅100%に変更 */
}

/* パスワード用 input */
.passwordWrapper input[type='text'],
.passwordWrapper input[type='password'] {
  width: 100%; /* 幅100% */
  padding-right: 36px; /* アイコン分の余白を確保 */
  box-sizing: border-box;
}

/* 👁️ ラベル（アイコン）のスタイル */
.togglePassword {
  position: absolute;
  top: 50%;
  right: 8px; /* input 内に収まるよう微調整 */
  transform: translateY(-50%);
  cursor: pointer;
  font-size: 18px;
  color: #666;
}
</style>
