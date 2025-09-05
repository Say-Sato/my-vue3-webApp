<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import ModalComponent from '@/components/ConfirmModal.vue'
import IconButton from '@/basics/IconButton.vue'

const route = useRoute() // 現在のルート情報を取得
const articleId = ref('') // URLから取得した記事IDを格納
const deleteMessage = ref('') // PHPから返されたメッセージを格納
const isModalVisible = ref(false) // モーダルの表示状態を管理
const modalMessage = ref('記事を削除しますか？')
const modalConfirmText = ref('実行')
const modalCancelText = ref('キャンセル')

const showModal = () => {
  isModalVisible.value = true // モーダルを表示
}

const onModalCancel = () => {
  console.log('モーダルがキャンセルされました。')
  isModalVisible.value = false // モーダルを閉じる
}

// 現在のURLから記事IDを取得
onMounted(() => {
  const path = route.path // 例: "/edit/25"
  const idMatch = path.match(/\/edit\/(\d+)$/) // 正規表現で末尾の数字を取得
  if (idMatch) {
    articleId.value = idMatch[1] // 記事IDを格納
  } else {
    deleteMessage.value = '記事IDが見つかりません'
  }
})

// 記事削除リクエストを送信
const deleteArticle = async () => {
  if (!articleId.value) {
    deleteMessage.value = '有効な記事IDがありません'
  }

  try {
    const phpUrl = `https://yellowokapi2.sakura.ne.jp/Vue/api/ArticleDeleteAPI.php/${articleId.value}`
    const response = await fetch(phpUrl)

    if (!response.ok) {
      throw new Error(`サーバーエラー: ${response.statusText}`)
    }

    const result = await response.json()

    // PHPからの結果を処理
    if (result['true']) {
      deleteMessage.value = result['true']
    } else {
      deleteMessage.value = result['error']
    }
  } catch (error: unknown) {
    if (error instanceof Error) {
      deleteMessage.value = `リクエストエラー: ${error.message}`
    } else {
      deleteMessage.value = '予期しないエラーが発生しました'
    }
  }
}
</script>

<template>
  <div>
    <div>
      <IconButton
        label="削除"
        class="delete-button"
        backgroundColor="danger"
        @click="showModal"
      />
      <ModalComponent
        v-if="isModalVisible"
        :message="modalMessage"
        :confirmText="modalConfirmText"
        :cancelText="modalCancelText"
        :isVisible="isModalVisible"
        :onConfirm="deleteArticle"
        :onCancel="onModalCancel"
      />
    </div>
    <p v-if="deleteMessage">{{ deleteMessage }}</p>
  </div>
</template>

<style scoped>
button {
  margin-top: 10px;
  padding: 10px 20px;
  background-color: #ff4d4f;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

button:hover {
  background-color: #ff7875;
}

p {
  margin-top: 10px;
  font-size: 16px;
}
</style>
