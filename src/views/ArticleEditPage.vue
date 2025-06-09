<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import EditForm from '@/pages/EntryPageView.vue'
import DeleteButton from '@/basics/ArticleDeleteButton.vue'

export default defineComponent({
  components: {
    EditForm,
    DeleteButton,
  },
  setup() {
    const route = useRoute() // 現在のルート情報を取得
    const router = useRouter() // ルーターインスタンスの作成
    const articleId = ref('') // URLから取得した記事IDを格納
    const deleteMessage = ref('') // PHPから返されたメッセージを格納

    const formData = ref<null | {
      title: string
      tags: string
      body: string
      publish: string
      adultsOnly: string
      imageBase64: string
    }>(null)

    const fetchInitialData = async () => {
      if (!articleId.value) {
        console.error('ログインIDが見つかりません')
      } else {
        try {
          const response = await fetch(
            `https://yellowokapi2.sakura.ne.jp/Vue/api/BlogAllCatchAPI.php/${articleId.value}`,
          )

          if (!response.ok) {
            throw new Error('データの取得に失敗しました')
          } else {
            const data = await response.json()
            formData.value = {
              title: data.title || '',
              tags: data.tag || '',
              body: data.body || '',
              publish: String(data.public ?? '0'),
              adultsOnly: String(data.R18 ?? '0'),
              imageBase64: data.url || '', // ここで初期値を設定
            }

            // 初期状態で画像が存在すれば FileReader を呼び出し
            if (formData.value.imageBase64) {
              handleImagePreview(formData.value.imageBase64)
            }
          }
        } catch (error) {
          console.error('エラーが発生しました:', error)
        }
      }
    }

    // 画像のプレビュー処理
    const handleImagePreview = (base64String: string) => {
      if (formData.value) {
        formData.value.imageBase64 = base64String // Base64データを直接設定
      }
    }

    // ファイル選択時の処理
    const handleImageChange = (event: Event) => {
      const file = (event.target as HTMLInputElement).files?.[0]
      if (!file) return

      const reader = new FileReader()
      reader.onload = () => {
        if (formData.value) {
          formData.value.imageBase64 = reader.result as string
        }
      }
      reader.readAsDataURL(file)
    }

    const submitFormData = async () => {
      if (!formData.value) {
        console.error('フォームデータが存在しません')
        return
      }

      // 変更のない部分を除外したデータを作成
      const updatedData = new FormData()
      if (formData.value.title)
        updatedData.append('title', formData.value.title)
      if (formData.value.tags) updatedData.append('tags', formData.value.tags)
      if (formData.value.body) updatedData.append('body', formData.value.body)
      if (formData.value.publish)
        updatedData.append('publish', formData.value.publish)
      if (formData.value.adultsOnly)
        updatedData.append('adultsOnly', formData.value.adultsOnly)
      if (formData.value.imageBase64)
        updatedData.append('imageBase64', formData.value.imageBase64)

      try {
        const response = await fetch(
          `https://yellowokapi2.sakura.ne.jp/Vue/api/UpdateArticleAPI.php/${articleId.value}`,
          {
            method: 'POST',
            body: updatedData,
          },
        )

        if (!response.ok) {
          throw new Error('データの送信に失敗しました')
        }

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
      //console.log('フォームデータを送信:', formData.value)
      submitFormData()
    }

    onMounted(() => {
      const path = route.path // 例: "/about/25"
      const idMatch = path.match(/\/about\/(\d+)$/) // 正規表現で末尾の数字を取得
      if (idMatch) {
        articleId.value = idMatch[1] // 記事IDを格納
      } else {
        deleteMessage.value = '記事IDが見つかりません'
      }
      fetchInitialData()
    })

    return {
      formData,
      handleImageChange,
      handleSubmit,
    }
  },
})
</script>

<template>
  <div>
    <h2>投稿編集</h2>

    <!-- ページ最上部に画像を表示 -->
    <div v-if="formData && formData.imageBase64" class="image-container">
      <img :src="formData.imageBase64" alt="記事の画像" class="image" />
    </div>

    <EditForm
      v-if="formData"
      :title="formData.title"
      :tags="formData.tags"
      :body="formData.body"
      :publish="formData.publish"
      :adultsOnly="formData.adultsOnly"
      :imageBase64="formData.imageBase64"
      @update:title="value => formData && (formData.title = value)"
      @update:tags="
        value => {
          if (formData) {
            formData.tags = value.replace(/[\s　,]+/g, ',')
          }
        }
      "
      @update:body="value => formData && (formData.body = value)"
      @update:publish="value => formData && (formData.publish = value)"
      @update:adultsOnly="value => formData && (formData.adultsOnly = value)"
      @handle-image-change="handleImageChange"
      @submit="handleSubmit"
    />
    <div v-else>
      <p>データを読み込み中...</p>
    </div>

    <DeleteButton />
  </div>
</template>

<style scoped>
.image-container {
  width: 95%; /* 画面全体の80%の幅 */
  height: 600px; /* 高さは固定（必要に応じて変更可） */
  display: block; /* ブロック要素として設定 */
  overflow: hidden; /* 画像が領域を超えた場合に隠す */
  background-color: #f0f0f0; /* 背景色を指定（オプション） */
  margin: 0 auto; /* 左右のマージンを自動で設定（中央寄せ） */
}

.image {
  width: 100%;
  height: 100%;
  object-fit: contain; /* 領域内に収める */
  object-position: center; /* 中央寄せにする */
}
</style>
