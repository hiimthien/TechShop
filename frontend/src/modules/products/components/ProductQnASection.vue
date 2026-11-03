<template>
  <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 md:p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-outline-variant/30 pb-4">
      <h2 class="text-base md:text-lg font-black text-on-surface uppercase tracking-wider flex items-center gap-2">
        <span class="w-1.5 h-5 bg-secondary rounded-full"></span>
        Hỏi và đáp về {{ productName }}
      </h2>
      <span class="text-xs text-on-surface-variant font-medium">
        ({{ totalQuestions }} câu hỏi)
      </span>
    </div>

    <!-- Quick Question Input Box (Phong cách CellphoneS) -->
    <form @submit.prevent="submitQuestion" class="p-4 bg-surface-container-low/40 rounded-2xl border border-outline-variant/40 space-y-3">
      <div class="flex items-center gap-2 text-xs font-bold text-on-surface">
        <span class="material-symbols-outlined text-primary text-[18px]">contact_support</span>
        <span>Bạn có câu hỏi cần TechShop giải đáp? Hãy gửi ngay tại đây:</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <input
          v-model="questionName"
          type="text"
          required
          placeholder="Họ và tên của bạn (bắt buộc)"
          class="px-3.5 py-2.5 bg-surface-container-lowest rounded-xl border border-outline-variant/60 text-xs focus:border-primary focus:ring-1 focus:ring-primary/20 text-on-surface"
        />
        <input
          v-model="questionPhone"
          type="tel"
          placeholder="Số điện thoại hoặc Email (để nhận thông báo khi có trả lời)"
          class="px-3.5 py-2.5 bg-surface-container-lowest rounded-xl border border-outline-variant/60 text-xs focus:border-primary focus:ring-1 focus:ring-primary/20 text-on-surface"
        />
      </div>

      <div class="relative">
        <textarea
          v-model="questionContent"
          rows="2"
          required
          placeholder="Xin mời nhập câu hỏi về bảo hành, khuyến mãi, tính năng hoặc thời gian giao hàng..."
          class="w-full px-3.5 py-2.5 bg-surface-container-lowest rounded-xl border border-outline-variant/60 text-xs focus:border-primary focus:ring-1 focus:ring-primary/20 text-on-surface"
        ></textarea>
      </div>

      <div class="flex items-center justify-between">
        <span v-if="feedbackMsg" class="text-xs font-bold text-emerald-600 flex items-center gap-1">
          <span class="material-symbols-outlined text-[16px]">check_circle</span>
          {{ feedbackMsg }}
        </span>
        <span v-else class="text-[11px] text-outline">
          * Đội ngũ kỹ thuật viên TechShop sẽ phản hồi trong vòng 5 - 10 phút.
        </span>

        <button
          type="submit"
          :disabled="isSubmitting"
          class="px-5 py-2 bg-primary hover:bg-primary-container text-on-primary rounded-xl font-bold text-xs uppercase tracking-wider transition-all disabled:opacity-50 shrink-0"
        >
          {{ isSubmitting ? 'Đang gửi...' : 'Gửi câu hỏi' }}
        </button>
      </div>
    </form>

    <!-- Q&A List -->
    <div class="space-y-4 divide-y divide-outline-variant/20">
      <div
        v-for="q in questions"
        :key="q.id"
        class="pt-4 first:pt-0 space-y-3"
      >
        <!-- Câu hỏi của khách -->
        <div class="flex items-start gap-3">
          <div class="w-8 h-8 rounded-full bg-secondary/15 text-secondary font-bold text-xs flex items-center justify-center uppercase shrink-0 mt-0.5">
            {{ q.customer_name ? q.customer_name.charAt(0) : 'K' }}
          </div>
          <div class="space-y-1">
            <div class="flex items-center gap-2">
              <span class="font-bold text-xs text-on-surface">{{ q.customer_name }}</span>
              <span class="text-outline text-[11px]">• {{ formatDate(q.created_at) }}</span>
            </div>
            <p class="text-xs text-on-surface leading-relaxed font-medium">
              {{ q.question }}
            </p>
          </div>
        </div>

        <!-- Câu trả lời của Quản trị viên TechShop -->
        <div
          v-if="q.admin_answer"
          class="ml-11 p-3.5 bg-primary/5 rounded-2xl border border-primary/20 space-y-1.5 text-xs relative"
        >
          <div class="flex items-center gap-2 font-bold text-primary text-[11px]">
            <span class="material-symbols-outlined text-[18px]">verified_user</span>
            <span>TechShop Chăm sóc khách hàng</span>
            <span class="text-outline text-[10px] font-normal">• {{ formatDate(q.answered_at || q.created_at) }}</span>
          </div>
          <p class="text-on-surface-variant leading-relaxed text-[11px]">
            {{ q.admin_answer }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { reviewService, type QuestionItem } from '../services/reviewService'
import echo from '@/core/services/echo'

const props = defineProps<{
  productId: number | string
  productName: string
}>()

const questions = ref<QuestionItem[]>([])
const totalQuestions = ref<number>(0)

const questionName = ref('')
const questionPhone = ref('')
const questionContent = ref('')
const isSubmitting = ref(false)
const feedbackMsg = ref('')

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

async function fetchQuestions() {
  try {
    const res = await reviewService.getQuestions(props.productId)
    questions.value = res.data
    totalQuestions.value = res.total
  } catch (err) {
    console.error('Lỗi khi tải câu hỏi:', err)
  }
}

async function submitQuestion() {
  if (!questionName.value.trim() || !questionContent.value.trim()) return

  isSubmitting.value = true
  feedbackMsg.value = ''

  try {
    const res = await reviewService.submitQuestion(props.productId, {
      customer_name: questionName.value.trim(),
      customer_phone: questionPhone.value.trim() || undefined,
      question: questionContent.value.trim(),
    })

    feedbackMsg.value = res.message || 'Gửi câu hỏi thành công!'
    questionContent.value = ''

    await fetchQuestions()

    setTimeout(() => {
      feedbackMsg.value = ''
    }, 3000)
  } catch (err) {
    console.error('Lỗi khi gửi câu hỏi:', err)
  } finally {
    isSubmitting.value = false
  }
}

function setupRealtimeListener() {
  if (!props.productId) return
  try {
    echo.channel(`product.${props.productId}`).listen('.QuestionCreated', (newQuestion: QuestionItem) => {
      const exists = questions.value.some((q) => q.id === newQuestion.id)
      if (!exists) {
        questions.value.unshift(newQuestion)
        totalQuestions.value += 1
      }
    })
  } catch (err) {
    console.warn('Realtime echo error:', err)
  }
}

onMounted(() => {
  fetchQuestions()
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

