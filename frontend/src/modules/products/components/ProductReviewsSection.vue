<template>
  <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 md:p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-outline-variant/30 pb-4">
      <h2 class="text-base md:text-lg font-black text-on-surface uppercase tracking-wider flex items-center gap-2">
        <span class="w-1.5 h-5 bg-primary rounded-full"></span>
        Đánh giá & Nhận xét {{ productName }}
      </h2>
    </div>

    <!-- Rating Summary Box (Phong cách CellphoneS) -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 p-4 md:p-6 bg-surface-container-low/40 rounded-2xl border border-outline-variant/30 items-center">
      <!-- 1. Điểm số trung bình -->
      <div class="md:col-span-4 flex flex-col items-center justify-center text-center border-b md:border-b-0 md:border-r border-outline-variant/30 pb-4 md:pb-0 pr-0 md:pr-4">
        <span class="text-4xl md:text-5xl font-black text-primary">
          {{ summary.average }} <span class="text-xl md:text-2xl text-outline font-semibold">/ 5</span>
        </span>
        <div class="flex items-center text-secondary my-2">
          <span
            v-for="s in 5"
            :key="s"
            class="material-symbols-outlined text-[20px]"
            :style="s <= Math.round(summary.average) ? 'font-variation-settings: \'FILL\' 1' : ''"
          >
            star
          </span>
        </div>
        <span class="text-xs text-on-surface-variant font-medium">
          Dựa trên <strong>{{ summary.total_reviews }}</strong> lượt đánh giá thực tế
        </span>
      </div>

      <!-- 2. Breakdown Progress Bars (5 -> 1 sao) -->
      <div class="md:col-span-5 space-y-2 text-xs">
        <div
          v-for="star in [5, 4, 3, 2, 1]"
          :key="star"
          class="flex items-center gap-2.5"
        >
          <span class="w-10 font-bold text-on-surface flex items-center gap-1 shrink-0">
            {{ star }} <span class="material-symbols-outlined text-[14px] text-secondary" style="font-variation-settings: 'FILL' 1">star</span>
          </span>
          <div class="flex-1 h-2.5 bg-surface-container rounded-full overflow-hidden">
            <div
              class="h-full bg-primary rounded-full transition-all duration-500"
              :style="{ width: getStarPercent(star) + '%' }"
            ></div>
          </div>
          <span class="w-8 text-right text-outline text-[11px] font-medium shrink-0">
            {{ summary.star_counts[star] || 0 }}
          </span>
        </div>
      </div>

      <!-- 3. Nút Viết Đánh Giá -->
      <div class="md:col-span-3 flex flex-col items-center justify-center text-center gap-2.5">
        <span class="text-xs text-on-surface-variant font-medium">Bạn đã từng sử dụng sản phẩm này?</span>
        <button
          type="button"
          @click="showReviewForm = !showReviewForm"
          class="w-full py-2.5 px-4 bg-primary hover:bg-primary-container text-on-primary rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-primary/20 active:scale-95 flex items-center justify-center gap-1.5"
        >
          <span class="material-symbols-outlined text-[18px]">rate_review</span>
          <span>{{ showReviewForm ? 'Đóng biểu mẫu' : 'Viết đánh giá ngay' }}</span>
        </button>
      </div>
    </div>

    <!-- Collapsible Form Viết Đánh Giá -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-2 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 -translate-y-2 scale-95"
    >
      <form
        v-if="showReviewForm"
        @submit.prevent="submitNewReview"
        class="p-5 bg-surface-container-lowest rounded-2xl border-2 border-primary/40 space-y-4 shadow-sm"
      >
        <h3 class="font-bold text-sm text-on-surface flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-[20px]">edit_note</span>
          Gửi đánh giá và nhận xét của bạn
        </h3>

        <!-- Chọn số sao -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
          <span class="text-xs font-bold text-on-surface">Đánh giá chung:</span>
          <div class="flex items-center gap-1">
            <button
              v-for="s in 5"
              :key="s"
              type="button"
              @click="formRating = s"
              class="text-secondary transition-transform hover:scale-125 p-0.5"
            >
              <span
                class="material-symbols-outlined text-[26px]"
                :style="s <= formRating ? 'font-variation-settings: \'FILL\' 1' : ''"
              >
                star
              </span>
            </button>
            <span class="text-xs font-bold text-primary ml-2">
              {{ ratingLabels[formRating] }}
            </span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <input
            v-model="formName"
            type="text"
            required
            placeholder="Họ và tên của bạn (bắt buộc)"
            class="px-3.5 py-2.5 bg-surface rounded-xl border border-outline-variant/60 text-xs focus:border-primary focus:ring-1 focus:ring-primary/20 text-on-surface"
          />
          <input
            v-model="formPhone"
            type="tel"
            placeholder="Số điện thoại (để xác nhận đã mua hàng)"
            class="px-3.5 py-2.5 bg-surface rounded-xl border border-outline-variant/60 text-xs focus:border-primary focus:ring-1 focus:ring-primary/20 text-on-surface"
          />
        </div>

        <textarea
          v-model="formContent"
          rows="3"
          required
          placeholder="Xin mời chia sẻ cảm nhận về sản phẩm (chất lượng, hiệu năng, thời lượng pin, dịch vụ giao hàng)..."
          class="w-full px-3.5 py-2.5 bg-surface rounded-xl border border-outline-variant/60 text-xs focus:border-primary focus:ring-1 focus:ring-primary/20 text-on-surface"
        ></textarea>

        <div class="flex items-center justify-between pt-1">
          <span v-if="submitFeedback" class="text-xs font-bold text-emerald-600 flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">check_circle</span>
            {{ submitFeedback }}
          </span>
          <span v-else></span>

          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-5 py-2 bg-primary hover:bg-primary-container text-on-primary rounded-xl font-bold text-xs uppercase tracking-wider transition-all disabled:opacity-50"
          >
            {{ isSubmitting ? 'Đang gửi...' : 'Gửi đánh giá' }}
          </button>
        </div>
      </form>
    </Transition>

    <!-- Star Filter Pills -->
    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-outline-variant/20">
      <span class="text-xs font-bold text-on-surface mr-1">Lọc đánh giá:</span>
      <button
        type="button"
        @click="filterStar = null"
        class="px-3 py-1 rounded-lg text-xs font-bold transition-all border"
        :class="
          filterStar === null
            ? 'border-primary bg-primary/10 text-primary shadow-xs'
            : 'border-outline-variant/40 text-on-surface hover:border-primary/50'
        "
      >
        Tất cả ({{ summary.total_reviews }})
      </button>
      <button
        v-for="st in [5, 4, 3, 2, 1]"
        :key="st"
        type="button"
        @click="filterStar = st"
        class="px-3 py-1 rounded-lg text-xs font-bold transition-all border flex items-center gap-1"
        :class="
          filterStar === st
            ? 'border-primary bg-primary/10 text-primary shadow-xs'
            : 'border-outline-variant/40 text-on-surface hover:border-primary/50'
        "
      >
        <span>{{ st }}</span>
        <span class="material-symbols-outlined text-[14px] text-secondary" style="font-variation-settings: 'FILL' 1">star</span>
        <span>({{ summary.star_counts[st] || 0 }})</span>
      </button>
    </div>

    <!-- Reviews List -->
    <div class="space-y-4 divide-y divide-outline-variant/20">
      <div
        v-for="rev in filteredReviews"
        :key="rev.id"
        class="pt-4 first:pt-0 space-y-2.5"
      >
        <!-- Reviewer Header -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <!-- Avatar chữ cái -->
            <div class="w-8 h-8 rounded-full bg-primary/15 text-primary font-bold text-xs flex items-center justify-center uppercase shrink-0">
              {{ rev.customer_name ? rev.customer_name.charAt(0) : 'U' }}
            </div>
            <div>
              <div class="flex items-center gap-2">
                <span class="font-bold text-xs text-on-surface">{{ rev.customer_name }}</span>
                <span
                  v-if="rev.is_verified_purchase"
                  class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md flex items-center gap-0.5"
                >
                  <span class="material-symbols-outlined text-[12px]">check_circle</span>
                  Đã mua tại TechShop
                </span>
              </div>
              <div class="flex items-center gap-2 text-[11px] text-outline mt-0.5">
                <div class="flex text-secondary">
                  <span
                    v-for="s in 5"
                    :key="s"
                    class="material-symbols-outlined text-[14px]"
                    :style="s <= rev.rating ? 'font-variation-settings: \'FILL\' 1' : ''"
                  >
                    star
                  </span>
                </div>
                <span>•</span>
                <span>{{ formatDate(rev.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Review Content -->
        <p class="text-xs leading-relaxed text-on-surface-variant pl-10">
          {{ rev.content }}
        </p>

        <!-- Admin Reply (Câu trả lời của Quản trị viên TechShop) -->
        <div
          v-if="rev.admin_reply"
          class="ml-10 p-3 bg-surface-container-low rounded-xl border border-outline-variant/30 space-y-1 text-xs"
        >
          <div class="flex items-center gap-2 font-bold text-primary text-[11px]">
            <span class="material-symbols-outlined text-[16px]">support_agent</span>
            <span>TechShop Quản trị viên</span>
            <span class="text-outline text-[10px] font-normal">• {{ formatDate(rev.replied_at || rev.created_at) }}</span>
          </div>
          <p class="text-on-surface-variant leading-relaxed text-[11px]">
            {{ rev.admin_reply }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { reviewService, type ReviewItem, type ReviewSummary } from '../services/reviewService'
import echo from '@/core/services/echo'

const props = defineProps<{
  productId: number | string
  productName: string
}>()

const summary = ref<ReviewSummary>({
  average: 5.0,
  total_reviews: 0,
  star_counts: { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 },
})
const reviews = ref<ReviewItem[]>([])
const filterStar = ref<number | null>(null)

const showReviewForm = ref(false)
const formRating = ref(5)
const formName = ref('')
const formPhone = ref('')
const formContent = ref('')
const isSubmitting = ref(false)
const submitFeedback = ref('')

const ratingLabels: Record<number, string> = {
  5: 'Cực kỳ hài lòng',
  4: 'Hài lòng',
  3: 'Bình thường',
  2: 'Chưa hài lòng',
  1: 'Rất thất vọng',
}

function getStarPercent(star: number): number {
  if (summary.value.total_reviews === 0) return 0
  const count = summary.value.star_counts[star] || 0
  return Math.round((count / summary.value.total_reviews) * 100)
}

const filteredReviews = computed(() => {
  if (filterStar.value === null) return reviews.value
  return reviews.value.filter((r) => r.rating === filterStar.value)
})

function formatDate(dateStr: string): string {
  if (!dateStr) return 'Gần đây'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('vi-VN', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
    })
  } catch {
    return 'Gần đây'
  }
}

async function fetchReviews() {
  try {
    const res = await reviewService.getReviews(props.productId)
    summary.value = res.summary
    reviews.value = res.data
  } catch (err) {
    console.error('Lỗi khi tải đánh giá sản phẩm:', err)
  }
}

async function submitNewReview() {
  if (!formName.value.trim() || !formContent.value.trim()) return

  isSubmitting.value = true
  submitFeedback.value = ''

  try {
    const res = await reviewService.submitReview(props.productId, {
      customer_name: formName.value.trim(),
      customer_phone: formPhone.value.trim() || undefined,
      rating: formRating.value,
      content: formContent.value.trim(),
    })

    submitFeedback.value = res.message || 'Đã gửi đánh giá thành công!'
    formContent.value = ''
    formRating.value = 5

    // Tải lại danh sách
    await fetchReviews()

    setTimeout(() => {
      showReviewForm.value = false
      submitFeedback.value = ''
    }, 2500)
  } catch (err) {
    console.error('Lỗi khi gửi đánh giá:', err)
  } finally {
    isSubmitting.value = false
  }
}

function setupRealtimeListener() {
  if (!props.productId) return
  try {
    echo.channel(`product.${props.productId}`).listen('.ReviewCreated', (newReview: ReviewItem) => {
      // Tránh trùng lặp nếu đã có trong danh sách
      const exists = reviews.value.some((r) => r.id === newReview.id)
      if (!exists) {
        reviews.value.unshift(newReview)
        summary.value.total_reviews += 1
        summary.value.star_counts[newReview.rating] = (summary.value.star_counts[newReview.rating] || 0) + 1
        const totalScore = Object.entries(summary.value.star_counts).reduce(
          (acc, [star, count]) => acc + Number(star) * count,
          0
        )
        summary.value.average = Number((totalScore / summary.value.total_reviews).toFixed(1))
      }
    })
  } catch (err) {
    console.warn('Realtime echo error:', err)
  }
}

onMounted(() => {
  fetchReviews()
  setupRealtimeListener()
})

onUnmounted(() => {
  if (props.productId) {
    try {
      echo.leaveChannel(`product.${props.productId}`)
    } catch {
      // Ignored
    }
  }
})
</script>

