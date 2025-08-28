<script setup lang="ts">
import { onMounted, ref, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import type { Image } from '@/types/PostResponse' // 型定義の場所に合わせてね
import IconButton from '@/basics/IconButton.vue'
import ImageList from '@/components/ArticleImageList.vue'

// props 定義
const props = defineProps<{
  id: string
  title: string
  tags: string
  body: string
  images: File[]
  publish: string
  adultsOnly: string
}>()

// router
const router = useRouter()

// フォーム用 reactive state
const formUserId = ref(props.id)
const formTitle = ref(props.title)
const formTags = ref(props.tags)
const formBody = ref(props.body)
const formPublish = ref(props.publish)
const formAdultsOnly = ref(props.adultsOnly)
const imageFiles = ref<File[]>(props.images || [])
const previewUrls = ref<string[]>([])
const previewImage = ref<Image[]>([])

// プレビュー画像生成
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

// submit 処理
const handleSubmit = async () => {
  const formData = new FormData()

  imageFiles.value.forEach(file => {
    formData.append('image[]', file)
  })

  formData.append('user_id', formUserId.value)
  formData.append('illust_title', formTitle.value)
  formData.append('tags', formTags.value)
  formData.append('ilust_body', formBody.value)
  formData.append('public', formPublish.value)
  formData.append('R18', formAdultsOnly.value)

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

// ✅ 画面幅が800px以上かを監視
const isWideScreen = ref(window.innerWidth > 800)
const updateScreenSize = () => {
  isWideScreen.value = window.innerWidth > 800
}

const fetchData = () => {
  previewImage.value = [] // 初期化しておく
  for (let i = 0; i < previewUrls.value.length; i++) {
    previewImage.value.push({
      image_id: i,
      image_url: previewUrls.value[i],
      sort_order: i,
    })
  }
}

onMounted(() => {
  window.addEventListener('resize', updateScreenSize)
  fetchData()
})
onBeforeUnmount(() => {
  window.removeEventListener('resize', updateScreenSize)
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
          <ImageList :images="previewImage ?? []" />
          <div class="title-favorite-wrapper">
            <h1 class="title">{{ formTitle || 'タイトルなし' }}</h1>
            <span class="favorite">
              <IconButton
                label="いいね"
                icon-class="fa-regular fa-heart"
                color="red"
              />
            </span>
          </div>

          <div class="dtl">{{ formBody || '本文が入っていません' }}</div>
          <!--<ArticleTags :tagsMsg="tagsArray" />-->
        </div>
      </div>

      <!-- フッターボタン -->
      <div class="footer-buttons">
        <IconButton label="戻る" @click="$router.back()" />
        <IconButton label="投稿する" @click="handleSubmit" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.container {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  width: 100%;
  padding-top: 60px; /* ヘッダーとの重なりを避ける */
  box-sizing: border-box;
  z-index: 9999;
}

/* プレビューカード本体 */
.preview-card {
  width: 90%;
  max-width: 960px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  padding: 20px;
  z-index: 10;
}

.main-layout {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.container-main {
  width: 100%;
}

.title-favorite-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.title {
  font-size: 24px;
  font-weight: bold;
}

.favorite {
  margin-left: 20px;
}

.dtl {
  line-height: 1.6;
  margin-bottom: 24px;
  white-space: pre-wrap; /* 改行を反映 */
}

.dtl::after {
  content: '';
  display: block;
  width: 100%;
  height: 1px;
  background-color: #eee;
  margin-top: 24px;
}

.footer-buttons {
  display: flex;
  justify-content: space-between; /* 左右に配置 */
  align-items: center; /* 高さ揃え */
  margin-top: 40px;
}

.btn {
  padding: 10px 20px;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  font-size: 14px;
}

.btn.cancel {
  background-color: #f0f0f0;
  color: #333;
}

.btn.submit {
  background-color: #3498db;
  color: white;
}
</style>
