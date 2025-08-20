<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { Image } from '@/views/ArticlePage.vue' // 型定義の場所に合わせてね
import EditForm from '@/pages/EntryPageView.vue'
import DeleteButton from '@/basics/ArticleDeleteButton.vue'
import ImageList from '@/components/ArticleImageList.vue'

export default defineComponent({
  components: {
    EditForm,
    DeleteButton,
    ImageList,
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
      images?: Image[] // ← ここ追加！
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

            if (data.tags) {
              // tagsをString型へ変換する
              const tagsString = data.tags.join(',')
              const tagsResponse = await fetch(
                `https://yellowokapi2.sakura.ne.jp/Vue/api/TagResolverAPI.php/${tagsString}`,
              )

              if (!tagsResponse.ok) {
                throw new Error('タグNameの取得に失敗しました')
              } else {
                const tagData = await tagsResponse.json()

                // tagsにtagData.tagNameを設定
                formData.value = {
                  title: data.title || '',
                  tags: tagData.tagName || '', // tagNameをここに代入
                  body: data.body || '',
                  publish: String(data.public ?? '0'),
                  adultsOnly: String(data.R18 ?? '0'),
                  imageBase64: data.url || '', // ここで初期値を設定
                  images: data.images || [], // ← 配列があれば追加！
                }
              }
            }

            // 初期状態で画像が存在すれば FileReader を呼び出し
            if (formData.value && formData.value.imageBase64) {
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
      const path = route.path // 例: "/edit/25"
      const idMatch = path.match(/\/edit\/(\d+)$/) // 正規表現で末尾の数字を取得
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
    >
      <template #top>
        <div v-if="formData && formData.images">
          <ImageList :images="formData?.images ?? []" :Edit="true" />
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
