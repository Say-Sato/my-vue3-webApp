<script setup lang="ts">
import { ref } from 'vue'
import FormComponent from '@/pages/EntryPageView.vue'
import PostPreview from '@/views/ArticlePreviewPage.vue'
import { useUserStore } from '@/stores/user'

const id = ref('')
const title = ref('')
const tags = ref('')
const body = ref('')
const publish = ref('')
const adultsOnly = ref('')
const images = ref<File[]>([]) // ← 配列に変更
const submitted = ref(false)
const tagList = ref<string[]>([])
const userStore = useUserStore()

id.value = userStore.id ?? '3'

const handleImagesChange = (newFiles: File[]) => {
  images.value = newFiles
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
      :images="images"
      @update:title="title = $event"
      @update:tags="updateTags"
      @update:body="body = $event"
      @update:publish="publish = $event"
      @update:adultsOnly="adultsOnly = $event"
      @update:images="handleImagesChange"
      @submit="handleSubmit"
    />

    <!-- プレビュー -->
    <PostPreview
      v-else
      :id="id"
      :title="title"
      :tags="tags"
      :body="body"
      :images="images"
      :publish="publish"
      :adultsOnly="adultsOnly"
      @reset="handleReset"
    />
  </div>
</template>
