<script lang="ts">
import { defineComponent, ref, watch } from 'vue'
import type { PropType } from 'vue'
import { useRouter } from 'vue-router'

export default defineComponent({
  props: {
    id: { type: String, required: true },
    title: { type: String, required: true },
    tags: { type: String, required: true },
    body: { type: String, required: true },
    image: {
      type: Object as PropType<File | null>, // ← ここがポイント💥
      required: false,
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
    const imageFile = ref<File | null>(props.image || null)

    const previewUrl = ref<string>('')

    // ファイルが変更されたらプレビューURLを更新
    watch(
      () => imageFile.value,
      newFile => {
        if (newFile) {
          previewUrl.value = URL.createObjectURL(newFile)
        }
      },
      { immediate: true },
    )

    const handleSubmit = async () => {
      const formData = new FormData()
      if (imageFile.value) {
        formData.append('image', imageFile.value)
      }
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
      imageFile,
      previewUrl,
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

      <!-- 画像プレビュー -->
      <div v-if="previewUrl">
        <img :src="previewUrl" alt="プレビュー画像" width="200" />
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
  width: 90%; /* コンテンツの幅を90%に設定 */
  height: 80%;
  margin: 0 auto; /* 中央に配置 */
}

.preview img {
  max-width: 100%;
  height: auto;
}
</style>
