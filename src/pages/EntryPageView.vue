<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import TextInput from '@/basics/TextInput.vue'
import IconButton from '@/basics/IconButton.vue'
import RadioInput from '@/basics/RadioInput.vue'
import PhotoDragDrop from '@/basics/PhotoDragDrop.vue'
import FormWrapper from '@/basics/FormWrapper.vue'

// props 定義
const props = defineProps<{
  title?: string
  tags?: string
  body?: string
  publish?: string
  adultsOnly?: string
  images?: File[]
}>()

// emit 定義
const emit = defineEmits<{
  (e: 'submit'): void
  (e: 'update:title', value: string): void
  (e: 'update:tags', value: string): void
  (e: 'update:body', value: string): void
  (e: 'update:publish', value: string): void
  (e: 'update:adultsOnly', value: string): void
  (e: 'update:images', value: File[]): void
}>()

// ルート
const route = useRoute()

// 編集画面判定
const hideEdit = computed(() => !/^\/posts\/edit\/\d+$/.test(route.path))

// computed for two-way binding
const localTitle = computed<string>({
  get: () => props.title ?? '',
  set: newValue => emit('update:title', newValue),
})

const localBody = computed<string>({
  get: () => props.body ?? '',
  set: newValue => emit('update:body', newValue),
})

const localPublish = computed<string>({
  get: () => props.publish ?? '',
  set: newValue => emit('update:publish', newValue),
})

const localAdultsOnly = computed<string>({
  get: () => props.adultsOnly ?? '',
  set: newValue => emit('update:adultsOnly', newValue),
})

const localImages = computed<File[]>({
  get: () => props.images ?? [],
  set: newValue => emit('update:images', newValue),
})

// タグ処理
const tagInput = ref('')
const tagList = ref<string[]>([])
const errorMessage = ref('')

const convertTagsToList = (tags: string) =>
  tags ? tags.split(',').map(tag => tag.trim()) : []

onMounted(() => {
  tagList.value = convertTagsToList(props.tags ?? '')
})

watch(
  () => props.tags,
  newTags => {
    tagList.value = convertTagsToList(newTags ?? '')
  },
)

const addTag = () => {
  const newTag = tagInput.value.trim()

  if (
    newTag &&
    !tagList.value.includes(newTag) &&
    !newTag.includes(',') &&
    !newTag.includes(' ') &&
    !newTag.includes('　')
  ) {
    tagList.value.push(newTag)
    tagInput.value = ''
    errorMessage.value = ''
    emitTagsUpdate()
  } else if (!newTag) {
    errorMessage.value = 'タグが入力されていません。'
  } else if (tagList.value.includes(newTag)) {
    errorMessage.value = 'タグが重複しています。'
  } else {
    errorMessage.value = '使用できない文字が含まれています。'
  }
}

const removeTag = (index: number) => {
  tagList.value.splice(index, 1)
  emitTagsUpdate()
}

const emitTagsUpdate = () => {
  emit('update:tags', tagList.value.join(','))
}

// submit
const handleSubmit = () => {
  emitTagsUpdate()
  emit('submit')
}
</script>

<template>
  <FormWrapper>
    <form @submit.prevent="handleSubmit">
      <!-- 画像アップロード -->
      <div v-if="hideEdit" class="image-upload">
        <PhotoDragDrop v-model="localImages" :maxCount="10" />
      </div>

      <div class="images-area" v-if="$slots.top">
        <slot name="top"></slot>
      </div>

      <!-- タイトル -->
      <p class="section-title">タイトル</p>
      <div class="form-group">
        <TextInput
          id="title"
          className="title"
          name="title"
          text="タイトル"
          v-model="localTitle"
          required
        />
      </div>

      <!-- エラーメッセージ -->
      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </div>

      <!-- タグ入力 -->
      <p class="section-title">タグ</p>
      <div class="form-group tag-input">
        <TextInput
          id="tags"
          className="tags"
          name="tags"
          text="タグ"
          v-model="tagInput"
        />
        <div class="add-button-wrapper">
          <IconButton label="追加" class="add-button" @click="addTag" />
        </div>
      </div>

      <!-- 登録済みタグ -->
      <div class="tag-list">
        <ul>
          <li v-for="(tag, index) in tagList" :key="index" class="tag-item">
            <span class="tag-label">{{ tag }}</span>
            <button class="tag-remove" @click="removeTag(index)">×</button>
          </li>
        </ul>
      </div>

      <!-- 本文 -->
      <p class="section-title">本文</p>
      <div class="form-group">
        <TextInput
          id="body"
          className="body"
          name="body"
          type="textarea"
          text="本文"
          v-model="localBody"
        />
      </div>

      <!-- 公開設定 -->
      <p class="section-title">公開設定</p>
      <div class="radio-buttons">
        <RadioInput
          id="public"
          name="publish"
          value="0"
          label="公開"
          v-model="localPublish"
          required
        />
        <RadioInput
          id="private"
          name="publish"
          value="1"
          label="非公開"
          v-model="localPublish"
          required
        />
      </div>

      <!-- 年齢制限 -->
      <p class="section-title">年齢制限</p>
      <div class="radio-buttons">
        <RadioInput
          id="allAges"
          name="AdultsOnly"
          value="0"
          label="全年齢"
          v-model="localAdultsOnly"
          required
        />
        <RadioInput
          id="adultsOnly"
          name="AdultsOnly"
          value="1"
          label="R18"
          v-model="localAdultsOnly"
          required
        />
      </div>

      <!-- プレビューボタン -->
      <div class="submit-area" v-if="$slots.bottom">
        <slot name="bottom"></slot>
        <IconButton label="保存" type="submit" />
      </div>

      <div class="submit-area-right" v-else>
        <IconButton label="プレビュー" type="submit" />
      </div>
    </form>
  </FormWrapper>
</template>

<style scoped>
/* 画像アップロード */
/* 共通フォーム要素 */
.form-group {
  margin-bottom: 16px;
}

.images-area {
  margin-bottom: 20px;
}

.title,
.tags,
.body {
  width: 100%;
}

/* タグ */
/* タグ入力欄をタイトルと同じ幅にする */
.tag-input {
  display: flex;
  flex-direction: column;
  gap: 8px; /* 入力欄とボタンの間隔 */
  margin-bottom: 16px; /* 他要素との間隔を一定に */
}

/* 追加ボタン */
.add-button-wrapper {
  display: flex;
  justify-content: flex-end; /* 右寄せ配置 */
}

.tag-list ul {
  list-style: none;
  padding: 8px;
  margin: 0;
}

.tag-item {
  display: inline-flex;
  align-items: center;
  background-color: #d8d8d8;
  color: #333;
  border-radius: 12px;
  padding: 4px 8px;
  margin: 4px;
  position: relative;
  font-size: 14px;
}

.tag-label {
  margin-right: 6px;
}

.tag-remove {
  background-color: white;
  color: black;
  border: none;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  font-size: 12px;
  line-height: 18px;
  text-align: center;
  cursor: pointer;
  padding: 0;
  transition: background-color 0.2s;
}

/* 本文 */
.body {
  min-height: 100px;
}

/* ラジオボタン */
.section-title {
  margin: 12px 0 6px;
  font-weight: bold;
  font-size: 14px;
}

/* 送信ボタン */
.submit-area {
  display: flex;
  justify-content: space-between; /* 左右に配置 */
  align-items: center; /* 高さ揃え */
  margin-top: 40px;
}

.submit-area-right {
  display: flex;
  justify-content: flex-end; /* 右寄せ */
  margin-top: 40px;
}

/* エラーメッセージ */
.error-message {
  color: red;
  margin-bottom: 12px;
}
</style>
