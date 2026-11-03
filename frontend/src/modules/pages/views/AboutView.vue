<template>
  <div class="max-w-container-max mx-auto px-lg py-12 md:py-16">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-xs text-xs text-on-surface-variant mb-8">
      <RouterLink to="/" class="hover:text-primary transition-colors">Trang chủ</RouterLink>
      <span class="material-symbols-outlined text-[16px]">chevron_right</span>
      <span class="text-on-surface font-medium">Giới thiệu</span>
    </nav>

    <!-- Hero Section -->
    <section class="text-center max-w-2xl mx-auto mb-16">
      <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full uppercase tracking-wider mb-3">
        Về chúng tôi
      </span>
      <h1 class="text-3xl md:text-5xl font-black text-on-surface mb-4 leading-tight">
        Đồng hành cùng tương lai <br /><span class="text-primary">Công nghệ</span> của bạn
      </h1>
      <p class="text-sm md:text-base text-on-surface-variant leading-relaxed">
        TechShop là hệ thống bán lẻ thiết bị công nghệ chính hãng hàng đầu Việt Nam. Chúng tôi cam kết mang lại sản phẩm chất lượng cao cùng dịch vụ hậu mãi chuẩn quốc tế.
      </p>
    </section>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-16">
      <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 text-center shadow-xs">
        <p class="text-3xl md:text-4xl font-black text-primary mb-1">100%</p>
        <p class="text-xs text-on-surface-variant font-medium">Hàng chính hãng</p>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 text-center shadow-xs">
        <p class="text-3xl md:text-4xl font-black text-primary mb-1">50+</p>
        <p class="text-xs text-on-surface-variant font-medium">Thương hiệu toàn cầu</p>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 text-center shadow-xs">
        <p class="text-3xl md:text-4xl font-black text-primary mb-1">200K+</p>
        <p class="text-xs text-on-surface-variant font-medium">Khách hàng tin tưởng</p>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 text-center shadow-xs">
        <p class="text-3xl md:text-4xl font-black text-primary mb-1">24/7</p>
        <p class="text-xs text-on-surface-variant font-medium">Hỗ trợ kỹ thuật</p>
      </div>
    </div>

    <!-- Core Values -->
    <section class="space-y-8">
      <div class="text-center max-w-xl mx-auto">
        <h2 class="text-2xl font-black text-on-surface">Giá trị cốt lõi</h2>
        <p class="text-xs md:text-sm text-on-surface-variant mt-1">Những tiêu chí hàng đầu tạo nên uy tín của TechShop</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/30 flex flex-col gap-3 shadow-xs">
          <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">verified</span>
          </div>
          <h3 class="text-base font-bold text-on-surface">Cam kết chính hãng</h3>
          <p class="text-xs leading-relaxed text-on-surface-variant">
            Toàn bộ sản phẩm được phân phối từ các đối tác ủy quyền chính thức của Apple, Asus, Dell, Samsung, Logitech... với đầy đủ hóa đơn VAT và bảo hành hãng.
          </p>
        </div>

        <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/30 flex flex-col gap-3 shadow-xs">
          <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">published_with_changes</span>
          </div>
          <h3 class="text-base font-bold text-on-surface">Hậu mãi vượt trội</h3>
          <p class="text-xs leading-relaxed text-on-surface-variant">
            Chính sách đổi mới trong 30 ngày đầu nếu phát sinh lỗi phần cứng từ nhà sản xuất. Hỗ trợ mượn máy thay thế trong thời gian bảo hành kỹ thuật.
          </p>
        </div>

        <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/30 flex flex-col gap-3 shadow-xs">
          <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">local_shipping</span>
          </div>
          <h3 class="text-base font-bold text-on-surface">Giao hàng thần tốc</h3>
          <p class="text-xs leading-relaxed text-on-surface-variant">
            Giao hàng hỏa tốc trong 2 giờ tại các thành phố lớn và từ 2 - 3 ngày trên toàn quốc. Đóng gói chuẩn công nghệ 3 lớp chống sốc tuyệt đối.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

