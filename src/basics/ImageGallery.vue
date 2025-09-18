<!-- UserPage etc. 汎用ギャラリー -->
<script setup lang="ts">
import { ref } from 'vue'
import type { Favorite } from '@/types/PostResponse'
import R18Modal from '@/components/Certificate18Modal.vue'

const props = withDefaults(
  defineProps<{
    posts: Favorite[]
    showLabel?: boolean
  }>(),
  {
    posts: () => [], // デフォルトは空配列
    showLabel: false, // デフォルト true
  },
)

// クリックで true にしたい変数
const isOverlayClicked = ref(false)

// タイトルを9文字で省略
const truncatedTitle = (illust_title: string) =>
  illust_title.length > 9 ? illust_title.slice(0, 9) + '…' : illust_title

// プロフィール写真のフルURL化
const fullProfilePhoto = (p_photo: string) =>
  `http://yellowokapi2.sakura.ne.jp/Blog/index${p_photo}`

const handleOverlayClick = () => {
  isOverlayClicked.value = true
}

// 子から close イベントを受け取ったら false に戻す
const handleClose = () => {
  isOverlayClicked.value = false
}
</script>

<template>
  <R18Modal v-if="isOverlayClicked" @close="handleClose" />
  <div class="gallery-container">
    <div v-for="post in props.posts" :key="post.illust_id" class="card">
      <div class="image-wrapper">
        <router-link
          v-if="!(post.R18 && !showLabel)"
          :to="`/posts/${post.illust_id}`"
        >
          <img
            :src="post.thumbnail_url"
            :alt="post.illust_title"
            class="card-image"
            :class="{ 'blurred-image': post.R18 && !showLabel }"
          />
        </router-link>

        <div v-else>
          <img
            :src="post.thumbnail_url"
            :alt="post.illust_title"
            class="card-image blurred-image"
          />
          <!-- R18オーバーレイ -->
          <div class="blur-overlay" @click="handleOverlayClick()">
            <span class="blur-text">閲覧注意</span>
          </div>
        </div>

        <div class="ap-image-gallery-label-container" v-if="showLabel">
          <div v-if="post.R18" class="ap-image-gallery-blur-label">R18</div>
          <div v-if="post.public" class="ap-image-gallery-private-label">
            非公開
          </div>
        </div>
      </div>

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
.image-wrapper {
  position: relative; /* ラベル配置の基準になる */
}

.ap-image-gallery-label-container {
  position: absolute;
  top: 8px;
  left: 8px;
  display: flex;
  gap: 4px;
  pointer-events: none;
}

.ap-image-gallery-blur-label {
  background: rgba(220, 20, 60, 0.7); /* クリムゾン系の赤 */
  color: white;
  font-size: 12px;
  padding: 2px 6px;
  border-radius: 4px;
}

.ap-image-gallery-private-label {
  background: rgba(0, 0, 0, 0.6);
  color: white;
  font-size: 12px;
  padding: 2px 6px;
  border-radius: 4px;
}

/* R18時のぼかし */
.blurred-image {
  filter: blur(12px);
}

/* オーバーレイ */
.blur-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.4);
  pointer-events: none;
}

.blur-text {
  color: #fff;
  font-size: 18px;
  font-weight: bold;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
}

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
