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
  <div class="container">
    <!-- プレビューカード -->
    <div class="preview-card">
      <div class="main-layout">
        <!-- メインエリア -->
        <div class="container-main">
          <!-- 画像エリア -->
          <div class="image-wrapper" v-if="previewUrls.length">
            <img
              v-for="(url, index) in previewUrls"
              :key="index"
              :src="url"
              alt="プレビュー画像"
              class="preview-image"
            />
          </div>

          <div class="title-favorite-wrapper">
            <!-- タイトル -->
            <h2 class="preview-title">{{ formTitle || 'タイトルなし' }}</h2>
            <span class="favorite">
              <IconButton label="いいね" />
            </span>
          </div>

          <div class="dtl">
            <!-- 本文 -->
            {{ formBody || '本文が入っていません' }}
          </div>
          <!-- ボタン群 -->
          <div class="action-buttons">
            <button class="btn">通知オン</button>
            <button class="btn">いいね</button>
            <button class="btn">フォロー</button>
            <button class="btn">フォロー</button>
            <button class="btn">フォロー</button>
            <button class="btn">フォロー</button>
          </div>
        </div>

        <!-- サイドエリア（画面幅に応じて表示切り替え） -->
        <div v-if="isWideScreen" class="sidebar">
          <div class="sidebar-divider"></div>
        </div>

        <div v-else class="sidebar-mobile">
          <div class="sidebar-divider"></div>
        </div>
      </div>

      <!-- フッターボタン -->
      <div class="footer-buttons">
        <button class="btn cancel">キャンセル</button>
        <button class="btn submit">投稿する</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.container {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: flex-start;
  width: 100%;
  box-sizing: border-box;
  position: relative;
}

/* プレビューカード本体 */
.preview-card {
  position: absolute;
  top: 40px;
  left: 50%;
  transform: translateX(-50%);
  width: 70%;
  max-width: 800px;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
  padding: 30px;
  text-align: center;
  z-index: 10;
}

.image-wrapper {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.preview-image {
  max-width: 100%;
  max-height: 400px;
  border-radius: 10px;
}

.preview-title {
  font-size: 22px;
  font-weight: bold;
  margin: 10px 0;
}

.preview-body {
  font-size: 14px;
  color: #444;
  margin: 15px 0 25px;
}

.main-layout {
  flex-direction: column;
  align-items: center; /* ← 中央揃えにするため追加 */
}

.container-main {
  width: 100%; /* ← 上書き */
  max-width: 800px; /* ← 任意で制限 */
  padding: 20px 0 0 0; /* ← paddingリセット */
  margin: 0 auto; /* ← 左右中央 */
  border-right: none; /* ← 線を消す */
}

.sidebar {
  display: none;
}

.sidebar-mobile {
  width: 100%;
  padding: 20px 0 0 0;
}
</style>
