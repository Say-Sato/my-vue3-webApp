<!-- components/CommentSection.vue -->
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import TextInput from '@/basics/TextInput.vue'
import IconButton from '@/basics/IconButton.vue'

export type PostResponse = {
  id: number
  user_id: number
  body: string
  profile_photo: string
  profile_name: string
  deleted_at: string
  comment_count: number
}

// ✅ propsを定義（nameとid）
const props = defineProps<{
  post_id: number
}>()

const posts = ref<PostResponse[]>([])

const fetchData = async () => {
  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/CommentAllCatchAPI.php/${props.post_id}`,
    )
    posts.value = await response.json()
  } catch (error) {
    console.error('Error fetching data:', error)
  }
}

onMounted(fetchData)
</script>

<template>
  <div class="container">
    <p class="count">コメント（3件）</p>

    <!-- コメント入力フォーム -->
    <div class="comment-group">
      <TextInput
        text="コメントを書く"
        type="textarea"
        class="comment-textarea"
      />
    </div>
    <div class="comment-submit">
      <IconButton label="送信" backgroundColor="#cbcbcb" />
    </div>

    <!-- コメントリスト -->
    <div class="comment-list">
      <template v-for="i in 3" :key="i">
        <div class="comment-item">
          <img
            src="https://placehold.jp/40x40.png"
            alt="ユーザーアイコン"
            class="comment-icon"
          />
          <div class="comment-body">
            <p class="comment-user">田原あろえ</p>
            <!-- 修正対象 -->
            <p class="comment-text">
              テスト仮メッセージああああああああああああああああ
            </p>
            <div class="comment-actions">
              <span class="comment-report">通報</span>
              <span class="comment-reply">返信</span>
            </div>
          </div>
        </div>
      </template>
    </div>
    <p class="show-all-comments">すべてのコメントを表示</p>
  </div>
</template>

<style scoped>
.container {
  background-color: #fff;
  width: 280px;
  padding: 10px;
  box-sizing: border-box;
  z-index: 1;
}

/* コメント数 */
.count {
  font-weight: bold;
  margin-bottom: 10px;
}

/* 入力フォーム */
.comment-group {
  width: 100%;
}

.comment-textarea {
  width: 100%;
  min-height: 60px;
  padding: 6px;
  border: 1px solid #ccc;
  border-radius: 6px;
  resize: vertical;
  box-sizing: border-box;
}

/* 送信ボタン */
.comment-submit {
  display: flex;
  justify-content: flex-end;
  margin: 10px 0;
}

/* コメントリスト */
.comment-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* コメント1件 */
.comment-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.comment-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.comment-body {
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* ユーザー名 */
.comment-user {
  font-weight: bold;
  margin: 0 0 6px;
}

/* コメントテキスト（修正ポイント） */
.comment-text {
  width: 100%;
  margin: 0 0 6px;
  line-height: 1.4;
  background-color: #f2f2f2;
  border-radius: 6px;
  padding: 8px;
  box-sizing: border-box;
}

/* アクション */
.comment-actions {
  font-size: 12px;
  color: #666;
  display: flex;
  gap: 12px;
}

.comment-report,
.comment-reply {
  cursor: pointer;
}

/* コメント全表示リンク */
.show-all-comments {
  text-align: right;
  font-size: 13px;
  color: #666;
  margin-top: 10px;
  cursor: pointer;
}

@media screen and (max-width: 800px) {
  .container {
    width: 100%;
  }
}
</style>
