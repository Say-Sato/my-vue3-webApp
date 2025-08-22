<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import EditForm from '@/pages/EntryPageView.vue'
import DeleteButton from '@/basics/ArticleDeleteButton.vue'
import ImageList from '@/components/ArticleImageList.vue'
import type { PostEdit } from '@/types/PostResponse'

const route = useRoute()
const router = useRouter()
const articleId = ref('')
const deleteMessage = ref('')
const formData = ref<PostEdit | null>(null) // 単一オブジェクトに変更！

const fetchInitialData = async () => {
  if (!articleId.value) {
    console.error('記事IDが見つかりません')
    return
  }

  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/BlogAllCatchAPI.php/${articleId.value}`,
    )

    if (!response.ok) throw new Error('データの取得に失敗しました')

    const data = await response.json()

    if (data.tags) {
      const tagsString = data.tags.join(',')
      const tagsResponse = await fetch(
        `https://yellowokapi2.sakura.ne.jp/Vue/api/TagResolverAPI.php/${tagsString}`,
      )

      if (!tagsResponse.ok) throw new Error('タグNameの取得に失敗しました')
      const tagData = await tagsResponse.json()

      // 型に合わせて代入
      formData.value = {
        illust_title: data.illust_title || '',
        tags: tagData.tagName || '',
        illust_body: data.illust_body || '',
        public: String(data.public ?? '0'),
        R18: String(data.R18 ?? '0'),
        thumbnail_url: data.thumbnail_url || '',
        images: data.images || [],
      }
    }

    if (formData.value?.thumbnail_url) {
      handleImagePreview(formData.value.thumbnail_url)
    }
  } catch (error) {
    console.error('エラーが発生しました:', error)
  }
}

// プレビュー処理
const handleImagePreview = (base64String: string) => {
  if (formData.value) {
    formData.value.thumbnail_url = base64String
  }
}

// ファイル選択時の処理
const handleImageChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = () => {
    if (formData.value) {
      formData.value.thumbnail_url = reader.result as string
    }
  }
  reader.readAsDataURL(file)
}

// フォーム送信
const submitFormData = async () => {
  if (!formData.value) {
    console.error('フォームデータが存在しません')
    return
  }

  const updatedData = new FormData()
  if (formData.value.illust_title)
    updatedData.append('illust_title', formData.value.illust_title)
  if (formData.value.tags) updatedData.append('tags', formData.value.tags)
  if (formData.value.illust_body)
    updatedData.append('illust_body', formData.value.illust_body)
  if (formData.value.public) updatedData.append('public', formData.value.public)
  if (formData.value.R18) updatedData.append('R18', formData.value.R18)
  if (formData.value.thumbnail_url)
    updatedData.append('thumbnail_url', formData.value.thumbnail_url)

  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/UpdateArticleAPI.php/${articleId.value}`,
      {
        method: 'POST',
        body: updatedData,
      },
    )

    if (!response.ok) throw new Error('データの送信に失敗しました')

    const result = await response.json()
    console.log('APIの応答:', result)
    alert('データが正常に送信されました')
    router.push({ path: `/article` })
  } catch (error) {
    console.error('エラーが発生しました:', error)
    alert('データの送信に失敗しました')
  }
}

const handleSubmit = () => {
  submitFormData()
}

onMounted(() => {
  const path = route.path
  const idMatch = path.match(/\/edit\/(\d+)$/)
  if (idMatch) {
    articleId.value = idMatch[1]
  } else {
    deleteMessage.value = '記事IDが見つかりません'
  }
  fetchInitialData()
})
</script>

<template>
  <div>
    <EditForm
      v-if="formData"
      :title="formData.illust_title"
      :tags="formData.tags"
      :body="formData.illust_body"
      :publish="formData.public"
      :adultsOnly="formData.R18"
      :imageBase64="formData.thumbnail_url"
      @update:title="value => formData && (formData.illust_title = value)"
      @update:tags="
        value => {
          if (formData) {
            formData.tags = value.replace(/[\s　,]+/g, ',')
          }
        }
      "
      @update:body="value => formData && (formData.illust_body = value)"
      @update:publish="value => formData && (formData.public = value)"
      @update:adultsOnly="value => formData && (formData.R18 = value)"
      @handle-image-change="handleImageChange"
      @submit="handleSubmit"
    >
      <template #top>
        <div v-if="formData.images">
          <ImageList :images="formData.images" :Edit="true" />
        </div>
      </template>

      <template #bottom>
        <DeleteButton />
      </template>
    </EditForm>

    <div v-else>
      <p>データを読み込み中...</p>
    </div>
  </div>
</template>

<style scoped>
.image {
  width: 100%;
  height: 100%;
  object-fit: contain; /* 領域内に収める */
  object-position: center; /* 中央寄せにする */
}
</style>
