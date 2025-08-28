<script setup lang="ts">
import type { Favorite } from '@/types/PostResponse'

const props = defineProps<{ posts: Favorite[] }>()

// タイトルを9文字で省略
const truncatedTitle = (illust_title: string) =>
  illust_title.length > 9 ? illust_title.slice(0, 9) + '…' : illust_title

// プロフィール写真のフルURL化
const fullProfilePhoto = (p_photo: string) =>
  `http://yellowokapi2.sakura.ne.jp/Blog/index${p_photo}`
</script>

<template>
  <div class="gallery-container">
    <div v-for="post in props.posts" :key="post.illust_id" class="card">
      <router-link :to="`/posts/${post.illust_id}`">
        <img
          :src="post.thumbnail_url"
          :alt="post.illust_title"
          class="card-image"
        />
      </router-link>

      <div class="card-body">
        <router-link :to="`/posts/${post.illust_id}`">
          <h3 class="card-title">
            {{ truncatedTitle(post.illust_title) }}
          </h3>
        </router-link>

        <router-link :to="`/user-profile/${post.profile_id}`">
          <div class="profile-info" v-if="post.showProfile">
            <img
              :src="fullProfilePhoto(post.profile_photo)"
              :alt="post.profile_name"
              class="profile-photo"
            />
            <span class="profile-name">{{ post.profile_name }}</span>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.gallery-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* デフォルトは4列 */
  gap: 16px;
  padding: 16px 0;
}

.card {
  display: flex;
  flex-direction: column;
  background-color: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease;
}

.card:hover {
  transform: translateY(-4px);
}

.card-image {
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
}

.card-body {
  padding: 8px;
}

.card-title {
  font-size: 14px;
  font-weight: 600;
  margin: 4px 0;
}

.profile-info {
  display: flex;
  align-items: center;
  gap: 6px;
}

.profile-photo {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-name {
  font-size: 12px;
  color: #555;
}

a {
  color: inherit;
  text-decoration: none;
}

/* プロフィール部分のリンクはblockではなくinline-flexにしてズレ防止 */
a.profile-info {
  display: inline-flex;
}

/* スマホ対応（1100px以下は2列表示） */
@media screen and (max-width: 800px) {
  .gallery-container {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}
</style>
