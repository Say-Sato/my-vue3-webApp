<script lang="ts">
import { defineComponent, ref } from 'vue'
import type { PropType } from 'vue'
import { useRouter } from 'vue-router'

export default defineComponent({
  props: {
    id: { type: String, required: true },
    title: { type: String, required: true },
    tags: { type: String, required: true },
    body: { type: String, required: true },
    images: {
      type: Array as PropType<File[]>,
      required: true,
      default: () => [],
    },
    publish: { type: String, required: true },
    adultsOnly: { type: String, required: true },
  },

  setup(props) {
    const router = useRouter()

    const formUserId = ref(props.id)
    const formTitle = ref(props.title)
    const formTags = ref(props.tags)
    const formBody = ref(props.body)
    const formPublish = ref(props.publish)
    const formAdultsOnly = ref(props.adultsOnly)
    const imageFiles = ref<File[]>(props.images || [])

    const previewUrls = ref<string[]>([])

    // 🔍 デバッグログ
    console.log('props.images:', props.images)
    console.log('imageFiles (before preview gen):', imageFiles.value)

    // プレビュー画像生成＋チェック
    previewUrls.value = imageFiles.value.map((file, i) => {
      if (!(file instanceof File)) {
        console.warn(`⚠️ imageFiles[${i}] は File 型じゃないよ：`, file)
      }
      try {
        return URL.createObjectURL(file)
      } catch (e) {
        console.error(`❌ createObjectURL 失敗 at index ${i}:`, e)
        return ''
      }
    })

    console.log('previewUrls:', previewUrls.value)

    const handleSubmit = async () => {
      const formData = new FormData()

      imageFiles.value.forEach(file => {
        formData.append('image[]', file)
      })

      formData.append('userid', formUserId.value)
      formData.append('title', formTitle.value)
      formData.append('tags', formTags.value)
      formData.append('body', formBody.value)
      formData.append('publish', formPublish.value)
      formData.append('adultsOnly', formAdultsOnly.value)

      try {
        const response = await fetch(
          'https://yellowokapi2.sakura.ne.jp/Vue/api/ArticleEntry.php',
          {
            method: 'POST',
            body: formData,
          },
        )

        if (!response.ok) throw new Error(`HTTPエラー: ${response.status}`)

        const contentType = response.headers.get('Content-Type') || ''
        if (contentType.includes('application/json')) {
          const result = await response.json()
          console.log('送信成功:', result)
          alert('データが正常に送信されました')
          router.push({ path: `/${formUserId.value}` })
        } else {
          console.log('レスポンスがJSONではありません。')
        }
      } catch (error) {
        console.error('送信エラー:', error)
      }
    }

    return {
      handleSubmit,
      formUserId,
      formTitle,
      formTags,
      formBody,
      formPublish,
      formAdultsOnly,
      imageFiles,
      previewUrls,
    }
  },
})
</script>

<template>
  <div class="preview">
    <h3>プレビュー</h3>
    <div>
      <h4>{{ formTitle }}</h4>
      <p>{{ formUserId || 'ID：null' }}</p>
      <p>{{ formTags || 'タグ：なし' }}</p>
      <p>{{ formBody || '本文：なし' }}</p>
      <p>publish:{{ formPublish }}</p>
      <p>adultsOnly:{{ formAdultsOnly }}</p>

      <!-- 画像プレビュースライダー -->
      <div class="image-preview-wrapper" v-if="previewUrls.length">
        <div class="image-row">
          <img
            v-for="(url, index) in previewUrls"
            :key="index"
            :src="url"
            alt="プレビュー画像"
            class="preview-image"
          />
        </div>
      </div>
    </div>

    <form @submit.prevent="handleSubmit">
      <button type="submit">送信</button>
    </form>
    <button @click="$emit('reset')">戻る</button>
  </div>
</template>

<style scoped>
.preview {
  background-color: #f4f4f4;
  padding: 20px;
  margin-top: 20px;
  border-radius: 5px;
  width: 90%;
  height: 80%;
  margin: 0 auto;
}

.image-preview-wrapper {
  overflow-x: auto;
  white-space: nowrap;
  padding-bottom: 10px;
  margin-top: 10px;
}

.image-row {
  display: flex;
  gap: 10px;
}

.preview-image {
  max-height: 150px;
  border-radius: 5px;
}
</style>
