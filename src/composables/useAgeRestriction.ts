import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { useAgeCheck } from '@/composables/useAgeCheck'

export function useAgeRestriction() {
  const userStore = useUserStore()
  const router = useRouter()
  const { isAdult } = useAgeCheck()

  const showModal = ref(false)
  const clickedPostId = ref<number | null>(null)

  const isAgeVerified = computed(() => {
    return userStore.birthDate ? isAdult(userStore.birthDate) : false
  })

  const handleAccessAttempt = (postId: number) => {
    if (isAgeVerified.value) {
      router.push({ path: `/posts/${postId}` })
    } else {
      if (userStore.birthDate) {
        alert(
          'このコンテンツは20歳以上の方限定です。年齢条件を満たしていない場合は閲覧できません。',
        )
      } else {
        alert('生年月日が未入力です。')
      }
    }
  }

  const showR18Modal = (postId: number) => {
    clickedPostId.value = postId
    if (userStore.certificate18) {
      router.push({ path: `/posts/${postId}` })
    } else {
      showModal.value = true
    }
  }

  const handleConfirm = () => {
    if (clickedPostId.value !== null) {
      handleAccessAttempt(clickedPostId.value)
    }
    handleClose()
  }

  const handleClose = () => {
    showModal.value = false
    clickedPostId.value = null
  }

  return {
    showModal,
    isAgeVerified,
    showR18Modal,
    handleConfirm,
    handleClose,
  }
}
