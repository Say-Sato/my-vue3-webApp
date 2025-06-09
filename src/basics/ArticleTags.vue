<script lang="ts">
import { ref, watch, defineComponent } from 'vue'

export default defineComponent({
  props: {
    tagsMsg: {
      type: Array as () => number[],
      default: () => [],
    },
  },

  setup(props) {
    const tags = ref<string[]>([])
    const tagNames = ref<number[]>([])

    const fetchTagNames = async () => {
      if (props.tagsMsg.length === 0) {
        tags.value = []
        tagNames.value = []
        return
      }

      const tagIdsString = props.tagsMsg.join(',')

      try {
        const response = await fetch(
          `https://yellowokapi2.sakura.ne.jp/Vue/api/TagResolverAPI.php/${tagIdsString}`,
        )

        const data = await response.json()

        if (data && data.tagName && data.tagIds) {
          tags.value = data.tagName.split(',')
          tagNames.value = data.tagIds
        } else {
          console.log('タグが取得できませんでした')
          tags.value = []
          tagNames.value = []
        }
      } catch (error) {
        console.error('タグの取得に失敗しました', error)
        tags.value = []
        tagNames.value = []
      }
    }

    watch(
      () => props.tagsMsg,
      async () => {
        await fetchTagNames()
      },
      { immediate: true },
    )

    return { tags, tagNames } // ← ここに tagNames を追加
  },
})
</script>

<template>
  <div class="tags-container">
    <ul>
      <li v-for="(tag, index) in tags" :key="index">
        <a :href="'/tags/' + tagNames[index]" target="_blank"> #{{ tag }} </a>
      </li>
    </ul>
  </div>
</template>

<style scoped>
/* 親コンテナのスタイル: <p> と <ul> を横並びにする */
.tags-container {
  display: flex; /* 親要素に flexbox を適用 */
  align-items: center; /* 中央に揃える（垂直方向） */
  gap: 10px; /* <p> と <ul> の間に10pxの隙間 */
}

/* <p>タグのスタイル */
.tags-label {
  margin: 0; /* デフォルトの余白を削除 */
  font-weight: bold; /* 太文字 */
}

/* ul のスタイル */
ul {
  list-style-type: none; /* デフォルトのリストスタイルをなくす */
  padding: 0; /* 左側の余白をなくす */
  display: flex; /* 横並びにする */
  gap: 10px; /* li の間隔を設定 */
}

/* li のスタイル */
li {
  background-color: #f0f0f0; /* 背景色を薄いグレーに */
  border-radius: 15px; /* 角を丸める */
  padding: 8px 16px; /* 内側の余白を設定 */
  font-size: 14px; /* フォントサイズ */
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* ほんのり影をつける */
  transition: background-color 0.3s ease; /* ホバー時に色が変わるアニメーション */
}

/* ホバー時のスタイル */
li:hover {
  background-color: #d0d0d0; /* ホバー時に背景色が変わる */
}

a {
  color: #333; /* リンクの文字色 */
  text-decoration: none; /* 下線なし */
}
</style>
