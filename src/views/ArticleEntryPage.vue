<script lang="ts">
import { defineComponent, ref } from 'vue'
import FormComponent from '@/pages/EntryPageView.vue'
import PostPreview from '@/views/ArticlePreviewPage.vue'
import { useUserStore } from '@/stores/user'

export default defineComponent({
  components: {
    FormComponent,
    PostPreview,
  },
  setup() {
    const id = ref('')
    const title = ref('')
    const tags = ref('')
    const body = ref('')
    const publish = ref('')
    const adultsOnly = ref('')
    const image = ref<File | null>(null) // ← 画像ファイル型
    const submitted = ref(false)
    const tagList = ref<string[]>([])
    const userStore = useUserStore()

    id.value = userStore.id ?? '3'

    const handleImageChange = (newFile: File) => {
      image.value = newFile
    }

    const updateTags = (newTags: string): void => {
      tagList.value = newTags.split(',').map(tag => tag.trim())
      tags.value = tagList.value.join(', ')
    }

    const handleSubmit = () => {
      submitted.value = true
    }

    const handleReset = () => {
      submitted.value = false
    }

    return {
      id,
      title,
      tags,
      body,
      publish,
      adultsOnly,
      image,
      submitted,
      handleImageChange,
      handleSubmit,
      handleReset,
      updateTags,
    }
  },
})
</script>

<template>
  <div>
    <!-- 入力フォーム -->
    <FormComponent
      v-if="!submitted"
      :title="title"
      :tags="tags"
      :body="body"
      :publish="publish"
      :adultsOnly="adultsOnly"
      :image="image"
      @update:title="title = $event"
      @update:tags="updateTags"
      @update:body="body = $event"
      @update:publish="publish = $event"
      @update:adultsOnly="adultsOnly = $event"
      @update:image="handleImageChange"
      @submit="handleSubmit"
    />

    <!-- プレビュー -->
    <PostPreview
      v-else
      :id="id"
      :title="title"
      :tags="tags"
      :body="body"
      :image="image"
      :publish="publish"
      :adultsOnly="adultsOnly"
      @reset="handleReset"
    />
  </div>
</template>
