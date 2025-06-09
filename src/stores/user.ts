// stores/user.ts
//ログイン情報保持用store
import { defineStore } from 'pinia'
import { useStorage } from '@vueuse/core' // ← これが便利！

export const useUserStore = defineStore('user', {
  state: () => ({
    id: useStorage('user_id', ''), // localStorageへ保存
    name: useStorage('user_name', ''), // 同上
    admin: useStorage('user_admin', 0), // 同上
    isLogin: useStorage('user_isLogin', false), //isLigin = *ログインしているときのみtrue*
  }),
  actions: {
    login(id: string, name: string, admin: number) {
      this.id = id
      this.name = name
      this.admin = admin
      this.isLogin = true
    },
    logout() {
      this.id = this.name = ''
      this.admin = 0
      this.isLogin = false
    },
  },
})
