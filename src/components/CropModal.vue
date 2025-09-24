<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import Cropper from 'cropperjs'
import IconButton from '@/basics/IconButton.vue'
import 'cropperjs/dist/cropper.css'
import FormWrapper from '@/basics/FormWrapper.vue'

const props = defineProps<{
  file: File | null
  show: boolean
}>()

const emit = defineEmits<{
  (e: 'cancel'): void
  (e: 'cropped', file: File): void
}>()

const imageRef = ref<HTMLImageElement | null>(null)
let cropper: Cropper | null = null

const setupCropper = () => {
  if (cropper) {
    cropper.destroy()
  }
  if (props.file && imageRef.value) {
    const reader = new FileReader()
    reader.onload = () => {
      if (imageRef.value) {
        imageRef.value.src = reader.result as string
        cropper = new Cropper(imageRef.value, {
          aspectRatio: 1,
          viewMode: 1,
          autoCropArea: 1,
          background: false,
        })
      }
    }
    reader.readAsDataURL(props.file)
  }
}

onMounted(setupCropper)
watch(() => props.file, setupCropper)

onBeforeUnmount(() => {
  cropper?.destroy()
})

const handleConfirm = () => {
  if (!cropper || !props.file) return
  const canvas = cropper.getCroppedCanvas({
    width: 500,
    height: 500,
  })
  canvas.toBlob(blob => {
    if (blob) {
      const croppedFile = new File([blob], props.file!.name, {
        type: props.file!.type,
      })
      emit('cropped', croppedFile)
    }
  }, props.file.type)
}

const handleCancel = () => {
  emit('cancel')
}
</script>

<template>
  <div v-if="show" class="modal-backdrop">
    <FormWrapper>
      <div class="crop-container">
        <div class="image-wrapper">
          <img ref="imageRef" style="max-width: 100%" />
        </div>
        <div class="buttons">
          <IconButton
            label="キャンセル"
            @click="handleCancel"
            type="button"
            backgroundColor="danger"
          />
          <IconButton label="決定" @click="handleConfirm" type="button" />
        </div>
      </div>
    </FormWrapper>
  </div>
</template>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000; /* 他の要素より手前に表示 */
}

.crop-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.image-wrapper {
  max-width: 400px;
  max-height: 400px;
  overflow: hidden;
}

.buttons {
  display: flex;
  justify-content: space-between; /* 左右に配置 */
  align-items: center; /* 高さ揃え */
  margin-top: 40px;
}

.confirm {
  background-color: #4caf50;
  color: white;
}

.cancel {
  background-color: #f44336;
  color: white;
}
</style>
