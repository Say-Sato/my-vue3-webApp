// composables/useAgeCheck.ts
export function useAgeCheck() {
  const calcAge = (birthDate: string | null | undefined): number | null => {
    if (!birthDate || birthDate.trim() === '') {
      // 値がない、または空文字のときは null を返す
      return null
    }

    const birth = new Date(birthDate)
    if (isNaN(birth.getTime())) {
      // 不正な日付文字列のときも null
      return null
    }

    const today = new Date()
    let age = today.getFullYear() - birth.getFullYear()
    const hasBirthdayPassed =
      today.getMonth() > birth.getMonth() ||
      (today.getMonth() === birth.getMonth() &&
        today.getDate() >= birth.getDate())

    if (!hasBirthdayPassed) {
      age--
    }

    return age
  }

  // 18歳以上かどうかを返す
  const isAdult = (birthDate: string | null | undefined): boolean => {
    const age = calcAge(birthDate)
    if (age === null) {
      // 値がない/空文字/不正な日付のときは「成人ではない」とする
      return false
    }
    return age >= 18
  }

  return { calcAge, isAdult }
}
