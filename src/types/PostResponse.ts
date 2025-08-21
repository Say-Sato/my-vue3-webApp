//基本となるどのコンポーネントでも使用する項目をまとめる
// 項目について同じものを指す変数名をコンポーネントごとに統一する
// 使用するAPIによって派生型を作成する

// 例示
export interface BasePost {
  id: number
  title: string
}

export interface PostSummary extends BasePost {
  // 例えば一覧でタグも見たいなら追加
  tags?: number[]
}

export interface PostDetail extends BasePost {
  body: string
  tags: number[]
  // export type Image
  images: Image[]
  prev_id: number
  next_id: number
  p_name: string
  p_photo: string
}

export type Image = {
  image_id: number
  image_url: string
  sort_order: number
}
