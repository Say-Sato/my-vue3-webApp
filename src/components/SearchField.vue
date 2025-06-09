<script lang="ts">
import TextInput from '@/basics/TextInput.vue'

export default {
  components: {
    TextInput,
  },
  methods: {
    handleInput(event: Event) {
      // event.target は HTMLInputElement なので、型をキャストします
      const target = event.target as HTMLInputElement
      console.log(target.value)
    },
  },
}
</script>

<template>
  <head>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
  </head>
  <div class="search-container">
    <div>
      <TextInput
        id="searchtext"
        className="searchtext"
        name="searchtext"
        @input="handleInput"
      />
    </div>
    <!-- 検索アイコン -->
    <button class="icon-button">
      <i class="fas fa-search"></i>
    </button>
  </div>
</template>

<style scoped>
.search-container {
  display: flex; /* 要素を横に並べる */
  justify-content: flex-end; /* 右端に配置 */
  align-items: center; /* 縦方向の中央揃え */
}

.icon-button {
  background-color: #cbcbcb; /* ボタンの背景色を透明に */
  border: 2px solid #7d7d7d; /* ボタンの枠線を黒色で指定 */
  color: black; /* アイコンの色を黒に設定 */
  padding: 8px 12px; /* 内側の余白 */
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
  border-radius: 20px; /* 角を丸くする */
}

.icon-button:hover {
  background-color: #caf611; /* ホバー時の色 */
}

.icon-button i {
  color: #fff;
  /* 縁取り */
  text-shadow: 1px 1px 0 #000;
}
</style>
