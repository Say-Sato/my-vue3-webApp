<script lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import ModalComponent from '@/components/ConfirmModal.vue'

export default {
  name: 'ArticleDeleteComponent',
  components: {
    ModalComponent,
  },
  data() {
    return {
      isModalVisible: false, // モーダルの表示状態を管理
      modalMessage: '記事を削除しますか？',
      modalConfirmText: '実行', // 右ボタンの文言
    }
  },
  methods: {
    showModal() {
      this.isModalVisible = true // モーダルを表示
    },
    onModalConfirm() {
      console.log('モーダルの「実行」がクリックされました！')
      this.isModalVisible = false // モーダルを閉じる
    },
    onModalCancel() {
      console.log('モーダルがキャンセルされました。')
      this.isModalVisible = false // モーダルを閉じる
    },
  },
  setup() {
    const route = useRoute() // 現在のルート情報を取得
    const articleId = ref('') // URLから取得した記事IDを格納
    const deleteMessage = ref('') // PHPから返されたメッセージを格納

    // 現在のURLから記事IDを取得
    onMounted(() => {
      const path = route.path // 例: "/about/25"
      const idMatch = path.match(/\/about\/(\d+)$/) // 正規表現で末尾の数字を取得
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
    return {
      articleId,
      deleteMessage,
      deleteArticle,
    }
  },
}
</script>

<template>
  <div>
    <div>
      <button @click="showModal">削除</button>
      <ModalComponent
        v-if="isModalVisible"
        :message="modalMessage"
        :confirmText="modalConfirmText"
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
