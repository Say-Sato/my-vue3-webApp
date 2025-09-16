// composables/useAgeCheck.ts
export function useAgeCheck() {
  const calcAge = (birthDate: string): number => {
    const today = new Date()
    const birth = new Date(birthDate)

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
  const isAdult = (birthDate: string): boolean => {
    return calcAge(birthDate) >= 18
  }

  return { calcAge, isAdult }
}
