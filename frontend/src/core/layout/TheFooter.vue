<template>
  <footer class="bg-surface-container border-t border-outline-variant full-width mt-16">
    <div
      class="w-full py-xl px-lg flex flex-col md:flex-row justify-between items-start md:items-center max-w-container-max mx-auto gap-xl"
    >
      <div class="flex flex-col gap-md">
        <RouterLink to="/" class="text-xl font-black text-primary">
          TechVina
        </RouterLink>
        <p class="text-sm text-on-surface-variant max-w-[320px] leading-relaxed">
          Hệ thống bán lẻ điện thoại, laptop và phụ kiện công nghệ hàng đầu Việt Nam.
        </p>
        <div class="text-xs text-on-surface-variant">
          © {{ currentYear }} TechVina Electronics. All rights reserved.
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-xl">
        <div class="flex flex-col gap-sm">
          <span class="font-bold text-sm text-on-surface">Về chúng tôi</span>
          <RouterLink class="text-on-surface-variant hover:text-primary text-xs transition-colors" to="/about">
            Giới thiệu
          </RouterLink>
          <RouterLink class="text-on-surface-variant hover:text-primary text-xs transition-colors" to="/contact">
            Hệ thống cửa hàng
          </RouterLink>
        </div>
        <div class="flex flex-col gap-sm">
          <span class="font-bold text-sm text-on-surface">Hỗ trợ</span>
          <RouterLink class="text-on-surface-variant hover:text-primary text-xs transition-colors" to="/contact">
            Liên hệ
          </RouterLink>
          <RouterLink class="text-on-surface-variant hover:text-primary text-xs transition-colors" to="/contact">
            Trung tâm bảo hành
          </RouterLink>
        </div>
        <div class="flex flex-col gap-sm">
          <span class="font-bold text-sm text-on-surface">Chính sách</span>
          <RouterLink class="text-on-surface-variant hover:text-primary text-xs transition-colors" to="/warranty-policy">
            Chính sách bảo hành
          </RouterLink>
          <RouterLink class="text-on-surface-variant hover:text-primary text-xs transition-colors" to="/shipping-policy">
            Giao hàng & Đổi trả
          </RouterLink>
        </div>
        <div class="flex flex-col gap-sm">
          <span class="font-bold text-sm text-on-surface">Thanh toán</span>
          <div class="text-xs text-on-surface-variant leading-relaxed">
            Hỗ trợ COD, VNPAY, MoMo và chuyển khoản ngân hàng.
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'

const currentYear = new Date().getFullYear()
</script>
