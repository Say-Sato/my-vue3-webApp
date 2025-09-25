// stores/user.ts
import { defineStore } from 'pinia'
import { useStorage } from '@vueuse/core'

// 共通部分
interface BaseUserData {
  name: string
  certificate18: number
  birthDate: string
}

// ユーザー全体の型
interface UserData extends BaseUserData {
  id: string
  admin: number
  login_id: string
  last_checked?: string
}

// プロフィール編集用
type EditUserData = BaseUserData

export const useUserStore = defineStore('user', () => {
  // state
  const id = useStorage('user_id', '')
  const name = useStorage('user_name', '')
  const login_id = useStorage('user_login_id', '')
  const admin = useStorage('user_admin', 0)
  const last_checked = useStorage('user_last_checked', '')
  const certificate18 = useStorage('user_certificate18', 0)
  const birthDate = useStorage('user_birthDate', '')
  const isLogin = useStorage('user_isLogin', false)

  // actions
  function login(userData: UserData) {
    id.value = userData.id
    name.value = userData.name
    admin.value = userData.admin
    login_id.value = userData.login_id
    last_checked.value = userData.last_checked || '1970-01-01 00:00:00'
    certificate18.value = userData.certificate18
    birthDate.value = userData.birthDate
    isLogin.value = true
  }
  function editProfile(editUserData: EditUserData) {
    name.value = editUserData.name
    certificate18.value = editUserData.certificate18
    birthDate.value = editUserData.birthDate
  }

  function logout() {
    id.value = ''
    name.value = ''
    login_id.value = ''
    last_checked.value = ''
    birthDate.value = ''
    admin.value = 0
    certificate18.value = 0
    isLogin.value = false
  }

  return {
    id,
    name,
    login_id,
    admin,
    last_checked,
    certificate18,
    birthDate,
    isLogin,
    login,
    editProfile,
    logout,
  }
})
