// types.ts

// コメント型
export type Comment = {
  comment_id: number
  user_id: number
  comment_body: string
  profile_photo: string
  profile_name: string
  deleted_at: string
}

// 投稿基本情報
export type PostResponse = {
  illust_id: number
  illust_title: string
  illust_body: string
  thumbnail_url: string
  profile_id: number
  profile_name: string
  profile_photo: string
  profile_login_id: string
}

// コメントレスポンス
export type CommentResponse = {
  success: boolean
  comment: Comment[]
}

// プロフィール情報
export type ProfileResponse = {
  profile_id: number
  profile_name: string
  profile_body: string
  profile_photo: string
  profile_certificate18: number
  profile_birthDate: string
}

// 投稿ギャラリー用の画像型
export type Image = {
  image_id: number
  image_url: string
  sort_order: number
}

export type PostEdit = {
  illust_title: string
  tags: string
  illust_body: string
  public: string
  R18: string
  thumbnail_url: string
  images?: Image[] // ← ここ追加！
}

// PostGallery（PostResponse を拡張）
export interface PostGallery extends PostResponse {
  tags: number[]
  images: Image[]
  prev_id: number
  next_id: number
  R18: number
}

// ImageGallery
export interface Favorite extends PostResponse {
  profile_id: number
  public: number
  R18?: number
  showProfile?: boolean
}
