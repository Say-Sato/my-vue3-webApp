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
    alert('画像ファイルを選んでね♡')
    return
  }

  const reader = new FileReader()
  reader.onload = () => {
    if (imagePreviewUrls.value.length >= MAX_IMAGES) {
      alert('画像は最大${MAX_IMAGES}枚までよッ！')
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
  newFiles => {
    imagePreviewUrls.value = []
    newFiles.forEach(file => {
      const reader = new FileReader()
      reader.onload = () => {
        imagePreviewUrls.value.push(reader.result as string)
      }
      reader.readAsDataURL(file)
    })
  },
  { immediate: true },
)
</script>

<template>
  <div class="image-upload">
    <label for="image">
      {{
        imagePreviewUrls.length > 0
          ? props.labelAfterText || '投稿画像：'
          : props.labelBeforeText || '画像をアップロード：'
      }}
    </label>

    <div class="drop-area" @dragover="onDragOver" @drop="onDrop">
      <input
        ref="fileInputRef"
        type="file"
        accept="image/*"
        multiple
        style="display: none"
        @change="
          e => {
            const files = (e.target as HTMLInputElement).files
            if (files) {
              Array.from(files)
                .slice(0, MAX_IMAGES - props.modelValue.length)
                .forEach(file => {
                  handleFile(file)
                })
            }
          }
        "
      />

      <label
        v-if="imagePreviewUrls.length === 0"
        class="click-area"
        @click="fileInputRef?.click()"
      >
        ここに画像をドラッグ＆ドロップ！<br />
        もしくはクリックして選択♡
      </label>

      <div v-else class="preview-scroll">
        <div
          v-for="(url, index) in imagePreviewUrls"
          :key="index"
          class="image-preview-wrapper"
        >
          <img :src="url" class="thumb" @click="fileInputRef?.click()" />
          <button class="remove-button" @click="clearImage(index)">×</button>
        </div>
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
  height: 200px; /* ← 明示的に縦幅を指定 */
  text-align: center;
  background: #f9f9f9;
  transition: 0.2s ease;
}

.drop-area:hover {
  border-color: #333;
  background: #eee;
}

.preview-scroll {
  display: flex;
  flex-direction: row; /* 横並び */
  overflow-x: auto;
  gap: 10px;
  padding: 10px 0;
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
  height: 180px; /* drop-area の高さと合わせて */
  display: flex;
  align-items: center; /* ← これで縦方向中央揃え */
  justify-content: center; /* 横方向も中央揃え（必要なら） */
  padding: 0; /* 念のため余白なしに */
  box-sizing: border-box; /* 枠のサイズにpaddingが含まれるように */
}

.thumb {
  height: auto;
  max-height: 100%; /* 枠内に収まる最大高さ */
  width: auto;
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
  transition: background-color 0.2s ease;
}

.remove-button:hover {
  background-color: #f88;
  color: white;
}
</style>
