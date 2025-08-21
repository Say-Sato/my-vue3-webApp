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
  <div class="main-layout">
    <!-- メインエリア -->
    <div class="container main">
      <!-- 画像スライダー -->
      <div class="image-container" v-if="previewUrls.length">
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

      <!-- タイトル・ユーザーID -->
      <div class="title-favorite-wrapper">
        <h1 class="title">{{ formTitle || 'タイトルなし' }}</h1>
        <span class="favorite">ID: {{ formUserId || 'null' }}</span>
      </div>

      <!-- 本文 -->
      <div class="dtl">{{ formBody || '本文が入っていません' }}</div>

      <!-- タグ -->
      <div class="article-tags">
        タグ：<span v-if="formTags && formTags.length">{{ formTags }}</span>
        <span v-else>なし</span>
      </div>

      <!-- 送信フォーム -->
      <form @submit.prevent="handleSubmit">
        <button type="submit">送信</button>
      </form>

      <!-- 戻るボタン -->
      <button @click="$emit('reset')">戻る</button>
    </div>

    <!-- サイドエリア（幅が広い場合のみ表示、仮値） -->
    <div class="sidebar">
      <div class="profile">投稿者: 仮ユーザー</div>
      <div class="sidebar-divider"></div>
      <div class="comment">コメントエリア</div>
    </div>
  </div>
</template>

<style scoped>
.main-layout {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: flex-start;
  width: 100%;
  box-sizing: border-box;
}

.container.main {
  width: calc(100% - 300px);
  padding: 20px;
  margin-left: -70px;
  margin-right: 20px;
  border-right: 2px dashed rgba(0, 0, 0, 0.2);
  box-sizing: border-box;
}

.image-container {
  position: relative;
  margin-top: 10px;
  display: flex;
  overflow-x: auto;
}

.image-row {
  display: flex;
  gap: 10px;
}

.preview-image {
  max-height: 150px;
  border-radius: 5px;
}

.title-favorite-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
}

.title {
  font-size: 20px;
  font-weight: bold;
  margin: 0;
}

.favorite {
  font-size: 14px;
  color: #666;
}

.dtl {
  line-height: 1.5;
  margin: 16px 0;
}

.dtl::after {
  content: '';
  display: block;
  width: 100%;
  height: 1px;
  background-color: #ccc;
  margin: 24px 0 10px;
}

.article-tags {
  margin-bottom: 16px;
}

.prev-next-wrapper {
  margin: 20px 0;
  display: flex;
  gap: 10px;
}

.sidebar {
  width: 250px;
  padding: 20px;
  box-sizing: border-box;
}

.sidebar-divider {
  width: 280px;
  height: 1px;
  background-color: #ccc;
  margin: 30px 0 10px;
}

/* モバイル対応 */
@media screen and (max-width: 800px) {
  .main-layout {
    flex-direction: column;
    align-items: center;
  }
  .container.main {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    border-right: none;
  }
  .sidebar {
    display: none;
  }
}
</style>
