<script setup lang="ts">
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import FollowButton from '@/components/FollowButton.vue'

import NotifyButton from '@/components/NotificationButton.vue'
import LinkCopy from '@/components/LinkCopy.vue'

const route = useRoute()

// id を数値化して整数チェック、そうじゃなければ null
const id = ref(route.params.id?.[0] ?? null)

const props = defineProps<{
  profile_photo?: string
  profile_name?: string
  profile_login_id?: string
}>()
</script>

<template>
  <div class="profile-container">
    <img
      :src="`http://yellowokapi2.sakura.ne.jp/Blog/index${props.profile_photo}`"
      alt="プロフィール画像"
      class="profile-photo"
    />
    <div class="profile-info">
      <h2>{{ props.profile_name }}</h2>
      <p>ID: {{ props.profile_login_id }}</p>
      <p>mailaddress@example.com</p>
    </div>
    <div class="profile-actions">
      <span v-if="id">
        <NotifyButton :n_id="Number(id)" />
      </span>
      <span v-if="id">
        <FollowButton :f_id="Number(id)" />
      </span>
      <LinkCopy />
    </div>
  </div>
</template>

<style scoped>
.profile-container {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 0;
  border-bottom: 1px solid #ccc;
}

.profile-photo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-info {
  flex: 1;
}

.profile-info h2 {
  margin: 0;
  font-size: 20px;
}

.profile-info p {
  margin: 4px 0;
  color: #555;
}

.profile-actions {
  display: flex;
  flex-direction: row;
  gap: 8px;
}
</style>
