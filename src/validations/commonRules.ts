import * as yup from 'yup'

export const requiredString = (msg = '必須です') => yup.string().required(msg)

export const minLength = (n: number, msg?: string) =>
  yup.string().min(n, msg ?? `${n}文字以上で入力してください`)

export const maxLength = (n: number, msg?: string) =>
  yup.string().max(n, msg ?? `${n}文字以内で入力してください`)

export const emailRule = (msg = '正しいメールアドレスを入力してください') =>
  yup.string().required('メールアドレスは必須です').email(msg)

// API経由でユーザー名が存在するかチェックする関数
async function checkUsernameExists(username: string): Promise<boolean> {
  const res = await fetch(
    `https://yellowokapi2.sakura.ne.jp/Vue/api/checkUsernamAPI.phpp?name=${username}`,
  )
  const data = await res.json()
  return data.exists // trueなら既にDBに存在
}

// Registerにて使用
export const usernameRule = (
  min = 5,
  max = 20,
  msg = 'このユーザーIDは既に使われています',
) =>
  yup
    .string()
    .required('ログインIDは必須です')
    .matches(/^[a-zA-Z0-9]+$/, 'ログインIDは半角英数字のみ使用できます')
    .min(min, `ログインIDは${min}文字以上で入力してください`)
    .max(max, `ログインIDは${max}文字以内で入力してください`)
    .test('unique-username', msg, async value => {
      if (!value) return true // 未入力は required で弾かれる
      const exists = await checkUsernameExists(value)
      return !exists
    })

// Registerにて使用
export const passwordRule = (min = 8, max = 32) =>
  yup
    .string()
    .required('パスワードは必須です')
    .min(min, `パスワードは${min}文字以上で入力してください`)
    .max(max, `パスワードは${max}文字以内で入力してください`)
    .matches(
      /^(?=.*[a-z])(?=.*[A-Z])|(?=.*[0-9])|(?=.*[!@#$%^&*])/,
      'パスワードは大小英字・数字・記号のうち2種類以上を含めてください',
    )

// Comment
export const commentRule = (min = 3, max = 500) =>
  yup
    .string()
    .required('コメントを入力してください')
    .trim() // 前後の空白を自動除去
    .min(min, `コメントは${min}文字以上で入力してください`)
    .max(max, `コメントは${max}文字以内で入力してください`)
    .matches(/^[^<>]*$/, 'コメントに使用できない文字が含まれています')

export const favoriteRule = (postValue?: number, userValue?: number) => {
  return yup
    .string()
    .required('ログインしてください')
    .test(
      'favorite-check',
      'postValue または userValue が空です',
      function (value) {
        // postValue または userValue が空ならエラー
        if (!postValue || !userValue) {
          return this.createError({
            message: 'postValue または userValue が空です',
          })
        }

        // postValue === userValue のときだけさらに値の入力をチェック
        if (postValue === userValue) {
          return (
            !!value ||
            this.createError({ message: 'コメントを入力してください' })
          )
        }

        // 条件に当てはまらなければバリデーション通過
        return true
      },
    )
}

export const folloowRule = (followerValue?: number, followedValue?: number) => {
  return yup
    .string()
    .required('コメントを入力してください')
    .test(
      'follow-check',
      'ログインユーザーはフォローできません',
      function (value) {
        // postValue === userValue のときだけチェック
        if (followerValue === followedValue) {
          // 値が空ならエラー
          return !!value
        }
        // 条件に当てはまらない場合はバリデーション通過
        return true
      },
    )
}

// 可変バリデーションの例
export const dynamicStringRule = (
  minLengthValue?: number,
  maxLengthValue?: number,
) => {
  let rule = yup.string().required('必須です')
  if (minLengthValue)
    rule = rule.min(
      minLengthValue,
      `${minLengthValue}文字以上で入力してください`,
    )
  if (maxLengthValue)
    rule = rule.max(
      maxLengthValue,
      `${maxLengthValue}文字以内で入力してください`,
    )
  return rule
}

export const confirmField = (refName: string, msg = '確認用が一致しません') =>
  yup.string().oneOf([yup.ref(refName)], msg)

// ファイル（画像）用バリデーションヘルパー
export const fileRule = (options?: {
  maxSize?: number
  types?: string[]
  required?: boolean
}) => {
  const maxSize = options?.maxSize ?? 5 * 1024 * 1024 // 5MBデフォルト
  const types = options?.types ?? ['image/png', 'image/jpeg', 'image/gif']

  let schema = yup.mixed<File>().nullable()

  if (options?.required) {
    schema = yup.mixed<File>().required('ファイルは必須です')
  }

  schema = schema
    .test(
      'fileSize',
      `ファイルは${Math.round(maxSize / 1024 / 1024)}MB以下である必要があります`,
      file => {
        if (!file) return !options?.required
        return file.size <= maxSize
      },
    )
    .test('fileType', '許可されていないファイル形式です', file => {
      if (!file) return !options?.required
      return types.includes(file.type)
    })

  return schema
}
