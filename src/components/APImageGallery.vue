<!-- ArticleCatchPage マイページギャラリー -->
<script setup lang="ts">
import { defineProps } from 'vue'
import type { Favorite } from '@/types/PostResponse'
import ImageGallery from '@/basics/ImageGallery.vue'

const props = defineProps<{ posts: Favorite[] }>()
</script>

<template>
  <ImageGallery :posts="props.posts" :showLabel="true" />
</template>
