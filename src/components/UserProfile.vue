<script setup lang="ts">
//import { computed } from 'vue'
import { defineProps } from 'vue'
import { useUserStore } from '@/stores/user'

import IconButton from '@/basics/IconButton.vue'

// ✅ propsを定義（nameとid）
const props = defineProps<{
  name?: string
  id?: number | string
  p_photo?: string
}>()

const userStore = useUserStore()

// ✅ props > store > fallback の順で表示名を決定
/*const newId = computed(() => {
  return userStore.id ?? '0'
})*/
</script>

<template>
  <div class="container">
    <div class="profile-row">
      <img
        v-if="props.p_photo"
        :src="`https://yellowokapi2.sakura.ne.jp/Blog/index${props.p_photo}`"
        alt="Profile Image"
        class="image"
      />
      <img
        v-else
        :src="`https://yellowokapi2.sakura.ne.jp/Blog/index/profile_photo/noimage.png`"
        alt="Sample Profile Image"
        class="image"
      />

      <div class="info">
        <p class="userName">{{ props.name || 'unknown' }} さん</p>
        <p class="userId">ID: {{ userStore.login_id }}</p>
      </div>

      <IconButton
        icon-class=""
        label="フォロー"
        backgroundColor="#ccc"
        textColor="white"
      />
    </div>
  </div>
</template>

<style scoped>
.container {
  background-color: #fff;
  width: 280px;
  padding: 10px;
  box-sizing: border-box;

  /* ← 追加ここから！ */
  z-index: 1;
}

.profile-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.image {
  width: 35px;
  height: 35px;
  object-fit: cover;
  border-radius: 50%;
  margin-right: 10px;
}

.info {
  display: flex;
  flex-direction: column;
  justify-content: center;
  flex: 1; /* 残りの幅を使ってボタンを右端に押し出す */
}

.userName,
.userId {
  font-size: 14px;
  margin: 2px 0;
}

.follow-btn {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 6px 12px;
  font-size: 12px;
  border-radius: 4px;
  cursor: pointer;
}

@media screen and (max-width: 800px) {
  .container {
    width: 100%;
    padding: 30px 0 0;
  }
}
</style>
