<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue'
import { useUserStore } from '@/stores/user'
import TextInput from '@/basics/TextInput.vue'
import IconButton from '@/basics/IconButton.vue'
import type { CommentResponse } from '@/types/PostResponse'

// ✅ propsを定義（post_id）
const props = defineProps<{
  post_id: number
}>()

const userStore = useUserStore()
const posts = ref<CommentResponse | null>(null)
const showAll = ref(false)
// 入力値
const commentBody = ref('')

// コメント送信処理
const submitComment = async () => {
  if (!commentBody.value.trim()) {
    alert('コメントを入力してください')
    return
  }
  const formData = new FormData()
  formData.append('post_id', props.post_id.toString())
  formData.append('user_id', userStore.id.toString())
  formData.append('body', commentBody.value)

  try {
    const response = await fetch(
      'https://yellowokapi2.sakura.ne.jp/Vue/api/CommentInsertAPI.php',
      {
        method: 'POST',
        body: formData,
      },
    )
    const result = await response.json()
    console.log('投稿結果' + result)
    if (result.success) {
      posts.value?.comment.push(result.comment) // 追加
      commentBody.value = ''
    } else {
      alert(result.error || '送信に失敗しました')
    }
  } catch (error) {
    console.error('Error:', error)
    alert('通信エラーです:')
  }
}

const fetchData = async () => {
  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/CommentAllCatchAPI.php/${props.post_id}`,
    )
    posts.value = await response.json()
    console.log(posts.value)
  } catch (error) {
    console.error('Error fetching data:', error)
  }
}

// ✅ 表示するコメント（3件 or 全件）
const visibleComments = computed(() => {
  if (!posts.value?.comment) return []
  return showAll.value ? posts.value.comment : posts.value.comment.slice(0, 3)
})

// ✅ コメント数
const commentCount = computed(() =>
  posts.value ? posts.value.comment.length : 0,
)

onMounted(() => {
  // マウント時にすでに post_id が入っていれば即 fetch
  if (props.post_id) {
    fetchData()
  }
})

// props.post_id がセットされたら fetch 実行
watch(
  () => props.post_id,
  newId => {
    if (newId) {
      fetchData()
    }
  },
)

//
watch(commentCount, () => {
  fetchData()
})
</script>

<template>
  <div class="container">
    <!-- コメント数 -->
    <p class="count">コメント( {{ commentCount }} )</p>

    <div class="comment-group">
      <TextInput
        v-model="commentBody"
        text="コメントを書く"
        type="textarea"
        class="comment-textarea"
      />
    </div>
    <div class="comment-submit">
      <IconButton
        label="送信"
        backgroundColor="secondary"
        @click="submitComment"
      />
    </div>

    <!-- コメントリスト -->
    <div
      v-if="commentCount > 0"
      class="comment-list"
      :class="{ scrollable: showAll && commentCount > 10 }"
    >
      <template v-for="c in visibleComments" :key="c.comment_id">
        <!-- 削除されていないコメント -->
        <div class="comment-item" v-if="!c.deleted_at">
          <img
            :src="
              c.profile_photo
                ? 'https://yellowokapi2.sakura.ne.jp/Blog/index' +
                  c.profile_photo
                : 'https://placehold.jp/40x40.png'
            "
            alt="ユーザーアイコン"
            class="comment-icon"
          />
          <div class="comment-body">
            <p class="comment-user">{{ c.profile_name }}</p>
            <p class="comment-text">{{ c.comment_body }}</p>
            <div class="comment-actions">
              <!--<span class="comment-report">通報</span>
        <span class="comment-reply">返信</span>-->
            </div>
          </div>
        </div>

        <!-- 削除済コメント -->
        <div class="comment-item" v-else>
          <img
            src="https://placehold.jp/40x40.png"
            alt="削除ユーザーアイコン"
            class="comment-icon"
          />
          <div class="comment-body">
            <p class="comment-user">deleted</p>
            <p class="comment-text" style="font-style: italic; color: #999">
              このコメントは削除されました
            </p>
            <div class="comment-actions">
              <span style="font-size: 0.8em; color: #bbb"
                >※このコメントは削除済みです</span
              >
            </div>
          </div>
        </div>
      </template>
    </div>
    <p v-else class="no-comments">コメントはありません</p>

    <!-- 「すべてのコメントを表示」リンク -->
    <p
      v-if="!showAll && commentCount > 3"
      class="show-all-comments"
      @click="showAll = true"
    >
      すべてのコメントを表示
    </p>
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

/* ✅ 10件を超える場合にスクロール可能にする */
.comment-list.scrollable {
  max-height: 400px; /* 好きな高さに調整可能 */
  overflow-y: auto;
  padding-right: 6px; /* スクロールバー用の余白 */
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

.comment-user {
  font-weight: bold;
  margin: 0 0 6px;
}

.comment-text {
  width: 100%;
  margin: 0 0 6px;
  line-height: 1.4;
  background-color: #f2f2f2;
  border-radius: 6px;
  padding: 8px;
  box-sizing: border-box;

  /* ここを追加 */
  white-space: normal; /* 自動改行を有効化 */
  word-break: break-word; /* 単語途中でも折り返す */
  overflow-wrap: break-word; /* こちらも折り返し補助 */
}

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
