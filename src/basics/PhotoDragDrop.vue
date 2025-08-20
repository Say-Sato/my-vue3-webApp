<script setup lang="ts">
import { ref, watch } from 'vue'

// ✅ props: v-modelとしてFile[]を受け取る
const props = defineProps<{
  modelValue: File[]
  labelBeforeText?: string
  labelAfterText?: string
  maxCount?: number
}>()

// ✅ emits: File[] を親に渡す
const emit = defineEmits<{
  (e: 'update:modelValue', value: File[]): void
}>()

const imagePreviewUrls = ref<string[]>([])
const fileInputRef = ref<HTMLInputElement | null>(null)
const MAX_IMAGES =
  props.maxCount && props.maxCount > 0 && props.maxCount <= 10
    ? props.maxCount
    : 1

// ファイル処理用関数
const handleFile = (file: File) => {
  if (!file.type.startsWith('image/')) {
    alert('画像ファイルを選んで')
    return
  }

  const reader = new FileReader()
  reader.onload = () => {
    if (imagePreviewUrls.value.length >= MAX_IMAGES) {
      alert('画像は最大${MAX_IMAGES}枚まで')
      return
    }

    imagePreviewUrls.value.push(reader.result as string)
    emit('update:modelValue', [...props.modelValue, file])
  }
  reader.readAsDataURL(file)
}

// ドロップエリア
const onDrop = (event: DragEvent) => {
  event.preventDefault()
  const files = event.dataTransfer?.files
  if (files) {
    Array.from(files)
      .slice(0, MAX_IMAGES - props.modelValue.length)
      .forEach(file => {
        handleFile(file)
      })
  }
}

const onDragOver = (event: DragEvent) => {
  event.preventDefault()
}

const clearImage = (index: number) => {
  imagePreviewUrls.value.splice(index, 1)
  const updated = [...props.modelValue]
  updated.splice(index, 1)
  emit('update:modelValue', updated)
}

// 親 → 子への反映（プレビューのみ）
watch(
  () => props.modelValue,
  async newFiles => {
    const urls: string[] = []

    for (const file of newFiles) {
      const dataUrl = await new Promise<string>(resolve => {
        const reader = new FileReader()
        reader.onload = () => resolve(reader.result as string)
        reader.readAsDataURL(file)
      })
      urls.push(dataUrl)
    }

    imagePreviewUrls.value = urls
  },
  { immediate: true },
)
</script>

<template>
  <div class="drop-area" @dragover="onDragOver" @drop="onDrop">
    <input
      ref="fileInputRef"
      type="file"
      accept="image/*"
      multiple
      style="display: none"
      @change="
        e => {
          const input = e.target as HTMLInputElement
          const files = input.files
          if (files) {
            Array.from(files)
              .slice(0, MAX_IMAGES - props.modelValue.length)
              .forEach(file => {
                handleFile(file)
              })
          }
          input.value = ''
        }
      "
    />

    <!-- 画像が0枚のとき -->
    <div v-if="imagePreviewUrls.length === 0" class="upload-placeholder">
      <label class="click-area image-button" @click="fileInputRef?.click()">
        画像を追加
      </label>
      <p class="upload-info" v-if="maxCount !== undefined && maxCount > 1">
        JPEG/PNG<br />{{ maxCount }}枚までアップロード可能
      </p>
    </div>

    <!-- 画像が1枚以上あるとき -->
    <div v-else class="preview-scroll">
      <label
        v-if="imagePreviewUrls.length < MAX_IMAGES"
        class="click-area image-button"
        @click="fileInputRef?.click()"
      >
        画像を追加
      </label>

      <div
        v-for="(url, index) in imagePreviewUrls"
        :key="index"
        class="image-preview-wrapper"
      >
        <img :src="url" class="thumb" />
        <button class="remove-button" @click="clearImage(index)">×</button>
      </div>
    </div>
  </div>

  <!-- 👇 登録画像数を drop-area 内右下に重ねる -->
  <p class="upload-count-overlay">
    {{ imagePreviewUrls.length }}/{{ MAX_IMAGES }}
  </p>
</template>

<style scoped>
.drop-area {
  padding: 20px;
  height: 200px;
  text-align: center;
  background: #ddd;
  border-radius: 8px;
  transition: 0.2s ease;
  position: relative; /* ← これで右下配置できる */
}

/* 画像数カウントを右下に配置 */
.upload-count-overlay {
  position: relative;
  bottom: 230px;
  left: 10px;
  font-size: 12px; /* ← 小さめ文字 */
  color: #666;
  margin: 0;
  pointer-events: none; /* 他の操作を邪魔しない */
  z-index: 10; /* 高いz-indexで前面に表示 */
}

/* 画像0枚のとき中央にボタンを配置 */
.upload-placeholder {
  display: flex;
  flex-direction: column; /* ボタンと文言を縦に並べる */
  align-items: center; /* 横方向中央寄せ */
  justify-content: center; /* drop-area 高さの中央に配置 */
  bottom: 100px;
  height: 100%;
}

.upload-info {
  margin-top: 8px; /* ボタンとの間隔 */
  text-align: center;
  font-size: 14px; /* upload-count-overlayより一段階大きい */
  color: #666;
  line-height: 1.3;
}

.preview-scroll {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  flex-direction: row;
  overflow-x: auto;
  overflow-y: hidden;
  gap: 10px;
  padding: 10px 0;
  height: 180px;
}

.image-button {
  width: 120px;
  height: 120px;
  min-width: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 14px;
  text-align: center;
  color: #555;
  background-color: #fff;
  border: 1px dashed #aaa;
  border-radius: 8px;
}

.click-area {
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.click-area:hover {
  background-color: #f0f0f0;
}

.image-preview-wrapper {
  position: relative;
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
}

.thumb {
  max-height: 100%;
  object-fit: contain;
  border: 1px solid #ccc;
  flex-shrink: 0;
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
}

.remove-button:hover {
  background-color: #f88;
  color: white;
}
</style>
