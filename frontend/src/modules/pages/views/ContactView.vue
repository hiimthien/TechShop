<template>
  <div class="max-w-container-max mx-auto px-lg py-12 md:py-16">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-xs text-xs text-on-surface-variant mb-8">
      <RouterLink to="/" class="hover:text-primary transition-colors">Trang chủ</RouterLink>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <span class="text-on-surface font-medium">Liên hệ & Hỗ trợ</span>
    </nav>

    <div class="max-w-5xl mx-auto space-y-12">
      <div class="text-center max-w-xl mx-auto">
        <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full uppercase tracking-wider mb-2">
          Hỗ trợ 24/7
        </span>
        <h1 class="text-3xl md:text-4xl font-black text-on-surface leading-tight">
          Liên hệ với TechVina
        </h1>
        <p class="text-xs md:text-sm text-on-surface-variant mt-2">
          Chúng tôi luôn sẵn sàng lắng nghe và giải đáp mọi thắc mắc của bạn về sản phẩm và dịch vụ.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 text-center flex flex-col items-center gap-3 shadow-xs">
          <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">call</span>
          </div>
          <h3 class="font-bold text-sm text-on-surface">Tổng đài hỗ trợ</h3>
          <p class="text-xs text-on-surface-variant">Tư vấn mua hàng & kỹ thuật</p>
          <a href="tel:19001234" class="text-primary font-black text-base hover:underline">1900 1234</a>
          <span class="text-[11px] text-outline">8:00 - 21:30 hàng ngày</span>
        </div>

        <!-- Card 2 -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 text-center flex flex-col items-center gap-3 shadow-xs">
          <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">mail</span>
          </div>
          <h3 class="font-bold text-sm text-on-surface">Email hỗ trợ</h3>
          <p class="text-xs text-on-surface-variant">Giải đáp khiếu nại & hợp tác</p>
          <a href="mailto:support@techvina.vn" class="text-primary font-bold text-sm hover:underline">support@techvina.vn</a>
          <span class="text-[11px] text-outline">Phản hồi trong 2 giờ</span>
        </div>

        <!-- Card 3 -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 text-center flex flex-col items-center gap-3 shadow-xs">
          <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">store</span>
          </div>
          <h3 class="font-bold text-sm text-on-surface">Hệ thống Showroom</h3>
          <p class="text-xs text-on-surface-variant">Trải nghiệm & bảo hành trực tiếp</p>
          <p class="text-xs font-semibold text-on-surface">Hà Nội & TP. Hồ Chí Minh</p>
          <span class="text-[11px] text-outline">Hơn 20 cửa hàng trên toàn quốc</span>
        </div>
      </div>

      <!-- Contact Form & Showrooms -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Showroom List (6 cols) -->
        <div class="lg:col-span-6 bg-surface-container-lowest p-6 md:p-8 rounded-2xl border border-outline-variant/30 space-y-4 shadow-xs">
          <h3 class="font-black text-lg text-on-surface pb-3 border-b border-outline-variant/20 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">location_on</span>
            Địa chỉ Showroom chính
          </h3>
          <div class="space-y-4 text-xs md:text-sm">
            <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/20">
              <h4 class="font-bold text-on-surface mb-1">Flagship Store Quận 1 (TP.HCM)</h4>
              <p class="text-on-surface-variant leading-relaxed">
                Số 123 Lê Lợi, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh
              </p>
              <p class="text-xs text-primary font-semibold mt-1">Hotline: 028 3822 1234</p>
            </div>
            <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/20">
              <h4 class="font-bold text-on-surface mb-1">Flagship Store Hoàn Kiếm (Hà Nội)</h4>
              <p class="text-on-surface-variant leading-relaxed">
                Số 45 Tràng Tiền, Phường Tràng Tiền, Quận Hoàn Kiếm, Hà Nội
              </p>
              <p class="text-xs text-primary font-semibold mt-1">Hotline: 024 3936 5678</p>
            </div>
          </div>
        </div>

        <!-- Contact Form (6 cols) -->
        <div class="lg:col-span-6 bg-surface-container-lowest p-6 md:p-8 rounded-2xl border border-outline-variant/30 space-y-4 shadow-xs">
          <h3 class="font-black text-lg text-on-surface pb-3 border-b border-outline-variant/20 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">send</span>
            Gửi tin nhắn cho chúng tôi
          </h3>

          <div v-if="sentSuccess" class="p-4 rounded-xl bg-secondary/10 text-secondary text-xs font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            Cảm ơn bạn! Tin nhắn của bạn đã được gửi thành công.
          </div>

          <form v-else @submit.prevent="handleSend" class="space-y-3">
            <div>
              <label class="text-xs font-semibold text-on-surface-variant block mb-1">Họ và tên *</label>
              <input
                v-model="form.name"
                required
                type="text"
                placeholder="Nguyễn Văn A"
                class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/50 text-xs bg-surface-container-low focus:outline-none focus:border-primary"
              />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-xs font-semibold text-on-surface-variant block mb-1">Email *</label>
                <input
                  v-model="form.email"
                  required
                  type="email"
                  placeholder="name@example.com"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/50 text-xs bg-surface-container-low focus:outline-none focus:border-primary"
                />
              </div>
              <div>
                <label class="text-xs font-semibold text-on-surface-variant block mb-1">Số điện thoại</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  placeholder="0901 234 567"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/50 text-xs bg-surface-container-low focus:outline-none focus:border-primary"
                />
              </div>
            </div>
            <div>
              <label class="text-xs font-semibold text-on-surface-variant block mb-1">Nội dung tin nhắn *</label>
              <textarea
                v-model="form.message"
                required
                rows="4"
                placeholder="Bạn cần hỗ trợ điều gì về sản phẩm, chính sách hoặc bảo hành?..."
                class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/50 text-xs bg-surface-container-low focus:outline-none focus:border-primary"
              ></textarea>
            </div>
            <button
              type="submit"
              class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold text-xs hover:bg-primary-container transition-all active:scale-[0.98] shadow-sm"
            >
              Gửi yêu cầu hỗ trợ
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink } from 'vue-router'

const sentSuccess = ref(false)
const form = ref({
  name: '',
  email: '',
  phone: '',
  message: '',
})

const handleSend = () => {
  sentSuccess.value = true
}
</script>

