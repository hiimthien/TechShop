import api from '@/core/api/axios'

export interface ReviewItem {
  id: number
  product_id: number
  customer_name: string
  customer_phone?: string | null
  rating: number
  content: string
  admin_reply?: string | null
  replied_at?: string | null
  is_verified_purchase: boolean
  created_at: string
}

export interface ReviewSummary {
  average: number
  total_reviews: number
  star_counts: Record<number, number>
}

export interface ReviewsResponse {
  status: boolean
  summary: ReviewSummary
  data: ReviewItem[]
}

export interface QuestionItem {
  id: number
  product_id: number
  customer_name: string
  question: string
  admin_answer?: string | null
  answered_at?: string | null
  created_at: string
}

export interface QuestionsResponse {
  status: boolean
  total: number
  data: QuestionItem[]
}

export const reviewService = {
  async getReviews(productId: number | string): Promise<ReviewsResponse> {
    const res = await api.get(`/products/${productId}/reviews`)
    return res.data
  },

  async submitReview(
    productId: number | string,
    payload: { customer_name: string; customer_phone?: string; rating: number; content: string }
  ): Promise<{ status: boolean; message: string; data: ReviewItem }> {
    const res = await api.post(`/products/${productId}/reviews`, payload)
    return res.data
  },

  async getQuestions(productId: number | string): Promise<QuestionsResponse> {
    const res = await api.get(`/products/${productId}/questions`)
    return res.data
  },

  async submitQuestion(
    productId: number | string,
    payload: { customer_name: string; customer_phone?: string; question: string }
  ): Promise<{ status: boolean; message: string; data: QuestionItem }> {
    const res = await api.post(`/products/${productId}/questions`, payload)
    return res.data
  },
}

