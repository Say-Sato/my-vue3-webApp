<script setup lang="ts">
import { onMounted, ref, defineProps } from 'vue'
import { useRoute } from 'vue-router'
import ImageGallery from '../components/FVImageGallery.vue'
import SectionTitle from '@/basics/SectionTitle.vue'
import SearchField from '../components/SearchField.vue'
import type { Favorite } from '@/types/PostResponse'

const route = useRoute() // Vue Router から現在のルートを取得

const props = defineProps<{
  keyword: string
}>()

// タグを保持する変数
const tag = ref('')
const posts = ref<Favorite[]>([])
const tagsName = ref('')

const fetchData = async () => {
  // クエリパラメータから tag を取得
  const queryTag = route.query.tag as string | undefined

  // tag が存在しない場合は fetch を中止
  if (!queryTag || queryTag.trim() === '') {
    console.log('タグが指定されていません。fetchを中止します。')
    return
  }

  tag.value = decodeURIComponent(queryTag)

  try {
    const response = await fetch(
      `https://yellowokapi2.sakura.ne.jp/Vue/api/TagCatchAPI.php?tag=${tag.value}`,
    )

    const data = await response.json()
    console.log('APIレスポンス:', data)

    if (Array.isArray(data)) {
      posts.value = data.map((post: Favorite) => ({
        ...post,
        showProfile: true,
      }))
    } else {
      console.error('APIから配列が返っていません:', data)
      posts.value = []
    }

    // 最初の要素の tags を格納
    if (data.length > 0 && data[0].tags) {
      tagsName.value = String(data[0].tags)
    }

    console.log('格納されたタグ:', tagsName.value)
  } catch (error) {
    console.error('Error fetching data:', error)
  }
}

// APIからデータを取得
onMounted(fetchData)
</script>

<template>
  <div class="container">
    <SearchField />
    {{ props.keyword }}
  </div>
  <div v-if="posts.length > 0">
    <!-- タグの表示 -->
    <div class="post-item" v-if="posts.length > 0">
      <SectionTitle
        :title="
          tagsName + 'の検索結果：' + `${posts.length}件` ||
          'error:タグ読み取り失敗'
        "
      />
    </div>

    <!-- 投稿データの表示 -->
    <ImageGallery :posts="posts" />
    <!-- posts 配列を渡す -->
  </div>
  <!-- データがなかった場合 -->
  <div v-else>
    <p>該当する検索結果はありません。</p>
  </div>
</template>

<style scoped>
.title {
  font-weight: 400;
  font-size: 24px;
  font-style: normal;
  border-bottom: 2px solid #777;
  padding-bottom: 10px;
}

.container {
  display: flex;
  padding-top: 10px;
}
</style>
