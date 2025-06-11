<script lang="ts">
import { watch, defineComponent, ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import TextInput from '@/basics/TextInput.vue'
import RadioInput from '@/basics/RadioInput.vue'
import PhotoDragDrop from '@/basics/PhotoDragDrop.vue'

export default defineComponent({
  components: {
    TextInput,
    RadioInput,
    PhotoDragDrop,
  },
  props: {
    title: String,
    tags: String,
    body: String,
    publish: String,
    adultsOnly: String,
    images: Array as () => File[],
  },
  emits: [
    'submit',
    'update:title',
    'update:tags',
    'update:body',
    'update:publish',
    'update:adultsOnly',
    'update:images',
  ],
  setup(props, { emit }) {
    const route = useRoute()

    const hideEdit = computed(() => {
      return !route.path.startsWith('/about')
    })

    const localTitle = computed<string>({
      get: () => props.title ?? '',
      set: newValue => emit('update:title', newValue),
    })

    const localBody = computed({
      get: () => props.body ?? '',
      set: newValue => emit('update:body', newValue),
    })

    const localPublish = computed({
      get: () => props.publish,
      set: newValue => emit('update:publish', newValue),
    })

    const localAdultsOnly = computed({
      get: () => props.adultsOnly,
      set: newValue => emit('update:adultsOnly', newValue),
    })

    const localImages = computed<File[]>({
      get: () => props.images ?? [],
      set: newValue => emit('update:images', newValue),
    })

    const tagInput = ref('')
    const tagList = ref<string[]>([])
    const errorMessage = ref<string>('')

    const convertTagsToList = (tags: string) => {
      return tags ? tags.split(',').map(tag => tag.trim()) : []
    }

    onMounted(() => {
      tagList.value = convertTagsToList(props.tags || '')
    })

    watch(
      () => props.tags,
      newTags => {
        tagList.value = convertTagsToList(newTags || '')
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

    const handleSubmit = () => {
      emitTagsUpdate()
      emit('submit')
    }

    return {
      localTitle,
      localBody,
      localPublish,
      localAdultsOnly,
      localImages,
      tagInput,
      tagList,
      errorMessage,
      addTag,
      removeTag,
      handleSubmit,
      hideEdit,
    }
  },
})
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <div>
      <label for="image">画像:</label>
      <PhotoDragDrop v-model="localImages" :maxCount="10" />
    </div>

    <div>
      <label for="title">タイトル</label>
      <TextInput
        id="title"
        class="title"
        name="title"
        v-model="localTitle"
        required
      />
    </div>

    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>

    <div>
      <label for="tags">タグ</label>
      <TextInput id="tags" class="tags" name="tags" v-model="tagInput" />
      <button type="button" @click="addTag">タグ追加</button>
    </div>
    <div>
      <p>登録済みタグ:</p>
      <ul>
        <li v-for="(tag, index) in tagList" :key="index">
          {{ tag }}
          <button type="button" @click="removeTag(index)">削除</button>
        </li>
      </ul>
    </div>

    <div>
      <label for="body">本文</label>
      <TextInput
        id="body"
        class="body"
        name="body"
        type="textarea"
        v-model="localBody"
      />
    </div>

    <p>公開設定</p>
    <div class="radio-buttons">
      <span class="radio">
        <RadioInput
          id="public"
          name="publish"
          value="0"
          label="公開"
          v-model="localPublish"
          required
        />
      </span>
      <span class="radio">
        <RadioInput
          id="private"
          name="publish"
          value="1"
          label="非公開"
          v-model="localPublish"
          required
        />
      </span>
    </div>

    <p>年齢制限</p>
    <div class="radio-buttons">
      <span class="radio">
        <RadioInput
          id="allAges"
          name="AdultsOnly"
          value="0"
          label="全年齢"
          v-model="localAdultsOnly"
          required
        />
      </span>
      <span class="radio">
        <RadioInput
          id="adultsOnly"
          name="AdultsOnly"
          value="1"
          label="R18"
          v-model="localAdultsOnly"
          required
        />
      </span>
    </div>
    <button type="submit">プレビュー</button>
  </form>
</template>

<style scoped>
ul {
  list-style: none;
  padding: 0;
}
li {
  display: flex;
  align-items: center;
  gap: 5px;
}
</style>
