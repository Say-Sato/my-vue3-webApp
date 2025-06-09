<script setup lang="ts">
import { ref } from 'vue'

const newId = ref<string>('') // ref の型を明示的に指定

// 初期値として localStorage の値をセット
newId.value = localStorage.getItem('name') ?? 'ゲスト'

//onMounted(async () => {
// localStorage に保存された値が現在の値と異なる場合にリロード
//newId.value = localStorage.getItem('name') ?? ''
//})
</script>

<template>
  <div class="container">
    <!--<img :src="`${profile?.profile_photo}`" alt="Sample Profile Image" class="image" />
    <p class="userName">{{ profile?.name || 'Unknown' }} さん</p>
    <p>{{ profile?.body || 'テスト本文テスト' }}</p>-->
    <p class="userName">{{ newId || 'unkonow' }} さん</p>

    <!-- ここで newId を表示 -->
  </div>
</template>

<style scoped>
.container {
  background-color: #fff; /* メニューの背景色 */
  position: absolute; /* メニューの位置を絶対指定 */
  top: 80px; /* ハンバーガーボタンの下に配置 */
  right: 0; /* 右端に配置 */
  width: 280px; /* メニューの幅 */
  z-index: 1; /* ハンバーガーボタンの下に表示 */
  padding: 10px;
  /* height は指定しない。コンテンツに合わせてサイズが決まる */
}

.container p {
  margin-top: 20px; /* 各コンテンツの上に20pxの余白を追加 */
}

.image {
  width: 60px;
  height: 60px;
  margin-top: 10px;
  object-fit: cover; /* 画像のアスペクト比を保ちながら、要素にぴったり収める */
  border-radius: 50%; /* 丸くする */
}
</style>
