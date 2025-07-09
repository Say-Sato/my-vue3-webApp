<!-- components/CommentSection.vue -->
<script setup lang="ts">
import TextInput from '@/basics/TextInput.vue'
import IconButton from '@/basics/IconButton.vue'
</script>

<template>
  <div class="comment-section">
    <p class="comment-count">コメント（123件）</p>

    <div class="comment-form">
      <TextInput
        text="コメントを書く"
        type="textarea"
        class="comment-textarea"
        style="width: 100%"
      />
      <IconButton
        iconClass=""
        label="送信"
        backgroundColor="#e6e6e6"
        textColor="black"
        class="comment-send-button"
      />
    </div>

    <!-- スクロール可能部分 -->
    <div class="comment-list">
      <template v-for="i in 3" :key="i">
        <div class="comment-icon"></div>
        <div class="comment-body">
          <p class="comment-user">田原あろえ</p>
          <p class="comment-text">
            試しにいいね押してみました。いいと思います。ああああああああああああああああ
          </p>
          <div class="comment-actions">
            <span class="comment-report">通報する</span>
            <span class="comment-reply">返信</span>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<style scoped>
.comment-section {
  background-color: #ffffff;
  width: 280px;
  padding: 10px;
  box-sizing: border-box;
}

/* コメント入力 */
.comment-form {
  position: relative;
  margin: 0 auto;
  width: 100%;
  padding-right: 100px;
}

.comment-send-button {
  position: absolute;
  right: 0;
  bottom: 5px;
  width: 80px; /* ボタンの固定幅 */
  height: 36px; /* ボタンの固定高さ */
  padding: 6px 12px;
}

.comment-input {
  flex: 1;
  padding: 6px 8px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

.comment-send {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  background-color: #eee;
  cursor: pointer;
}

/* スクロール対象 */
.comment-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  overflow-y: auto;
  padding-right: 4px; /* スクロールバーと余白を分けるため */
}

/* コメントアイコンと本文 */
.comment-icon {
  width: 36px;
  height: 36px;
  background-color: #ccc;
  border-radius: 50%;
}

.comment-body {
  flex: 1;
}

.comment-user {
  font-weight: bold;
  font-size: 14px;
  margin: 4px 0 2px;
}

.comment-textarea {
  padding-right: 100px; /* ← ボタンの幅80px + マージン的に20pxくらい */
}

.comment-actions {
  font-size: 12px;
  color: gray;
  display: flex;
  gap: 16px;
}

.comment-report,
.comment-reply {
  cursor: pointer;
  text-decoration: underline;
}
@media screen and (max-width: 800px) {
  .comment-section {
    width: 100%; /* ← 親要素の幅にフィット */
    background-color: #fff;
    padding: 10px;
    box-sizing: border-box;
    margin-top: 16px; /* ← Profileとの間にスペース */
  }
  .comment-form {
    width: 100%;
  }
}
</style>
