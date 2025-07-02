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
      @input="handleInput"
    ></textarea>

    <!-- password -->
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
        @input="handleInput"
      />
    </template>
  </div>
</template>

<style scoped>
.textInput input[type='text'],
.textInput input[type='password'],
.textInput textarea {
  margin-right: 5px;
  height: 32px;
  padding: 1px 12px;
  border-radius: 20px;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

/* textarea用 */
.textInput textarea {
  width: 190px;
  height: 100px;
  resize: vertical;
  border-width: 2px;
}

/* パスワード入力用ラッパー */
.passwordWrapper {
  position: relative;
  width: 190px;
}

/* パスワード用 input */
.passwordWrapper input {
  padding-right: 36px;
  width: 100%;
  box-sizing: border-box;
  text-align: left;
}

/* 👁️ ラベル（アイコン）のスタイル */
.togglePassword {
  position: absolute;
  top: 50%;
  right: -30px;
  transform: translateY(-50%);
  cursor: pointer;
  font-size: 18px;
  color: #666;
}
</style>
