<script setup lang="ts">
import { ref, watch } from 'vue'

// ✅ props: v-modelとしてFile型を受け取る
const props = defineProps<{
  modelValue: File | null
  labelBeforeText?: string
  labelAfterText?: string
}>()

// ✅ emits: File型のデータを親に渡す
const emit = defineEmits<{
  (e: 'update:modelValue', value: File | null): void
}>()

const imagePreviewUrl = ref<string | null>(null)
const fileInputRef = ref<HTMLInputElement | null>(null)

// ファイル処理用関数
const handleFile = (file: File) => {
  if (!file.type.startsWith('image/')) {
    alert('画像ファイルを選んでね♡')
    return
  }

  const reader = new FileReader()
  reader.onload = () => {
    imagePreviewUrl.value = reader.result as string
  }
  reader.readAsDataURL(file)

  emit('update:modelValue', file)
}

// ドロップエリア
const onDrop = (event: DragEvent) => {
  event.preventDefault()
  const files = event.dataTransfer?.files
  if (files && files.length > 0) {
    handleFile(files[0])
  }
}

const onDragOver = (event: DragEvent) => {
  event.preventDefault()
}

const clearImage = () => {
  imagePreviewUrl.value = null
  emit('update:modelValue', null)
}

// 親 → 子への反映（プレビューのみ）
watch(
  () => props.modelValue,
  newFile => {
    if (!newFile) {
      imagePreviewUrl.value = null
      return
    }

    const reader = new FileReader()
    reader.onload = () => {
      imagePreviewUrl.value = reader.result as string
    }
    reader.readAsDataURL(newFile)
  },
)
</script>

<template>
  <div class="image-upload">
    <label for="image">
      {{
        imagePreviewUrl
          ? props.labelAfterText || '投稿画像：'
          : props.labelBeforeText || '画像をアップロード：'
      }}
    </label>

    <!-- ドロップエリア -->
    <!-- ドロップエリア -->
    <div class="drop-area" @dragover="onDragOver" @drop="onDrop">
      <input
        ref="fileInputRef"
        type="file"
        accept="image/*"
        style="display: none"
        @change="
          e => {
            const files = (e.target as HTMLInputElement).files
            if (files && files[0]) handleFile(files[0])
          }
        "
      />

      <label
        v-if="!imagePreviewUrl"
        class="click-area"
        @click="fileInputRef?.click()"
      >
        ここに画像をドラッグ＆ドロップ！<br />
        もしくはクリックして選択♡
      </label>

      <!-- 🆕 プレビュー＋×削除ボタン -->
      <div v-if="imagePreviewUrl" class="image-preview-wrapper">
        <img
          :src="imagePreviewUrl"
          class="thumb"
          @click="fileInputRef?.click()"
        />
        <button class="remove-button" @click="clearImage">×</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.image-upload {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.drop-area {
  border: 2px dashed #999;
  padding: 20px;
  height: 200px;
  text-align: center;
  background: #f9f9f9;
  cursor: pointer;
  transition: 0.2s ease;

  /* 🌟 追加：中央寄せ用Flexbox */
  display: flex;
  align-items: center;
  justify-content: center;
}
.drop-area:hover {
  border-color: #333;
  background: #eee;
}

.thumb {
  /* drop-areaの高さに揃える！ */
  height: 200px;

  /* 縦横比保ったままフィットさせる */
  object-fit: contain;

  /* 他の装飾 */
  border: 1px solid #ccc;
  max-width: 100%; /* 横幅は親に合わせて */
}

.click-area {
  cursor: pointer;
  display: inline-block;
  padding: 10px;
  border: 1px dashed #aaa;
  border-radius: 8px;
  background-color: #fff;
  transition: background-color 0.3s ease;
}

.click-area:hover {
  background-color: #f0f0f0;
}

.image-preview-wrapper {
  position: relative;
  display: inline-block;
}

.remove-button {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: rgba(255, 255, 255, 0.8);
  border: 1px solid #aaa;
  border-radius: 50%;
  font-weight: bold;
  cursor: pointer;
  width: 24px;
  height: 24px;
  text-align: center;
  line-height: 22px;
  padding: 0;
  font-size: 16px;
  color: #333;
  transition: background-color 0.2s ease;
}

.remove-button:hover {
  background-color: #f88;
  color: white;
}
</style>
